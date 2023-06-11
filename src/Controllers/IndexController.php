<?php

namespace MyBlog\Controllers;

use Exception;
use JasonGrimes\Paginator;
use MyBlog\Core\Routing\Annotation\Route;
use MyBlog\Core\Traits\ToJsonStringTrait;
use MyBlog\Exceptions\ResourceNotFoundException;
use MyBlog\Middlewares\IsAdmin;
use MyBlog\Repositories\PostRepository;
use MyBlog\ViewModels\IndexView;
use Symfony\Component\HttpFoundation\Request;

class IndexController extends BaseController
{
    use ToJsonStringTrait;

    public function __construct
    (
        private readonly PostRepository $postRepository,
        private readonly IndexView      $view
    )
    {
    }

    /**
     * @throws ResourceNotFoundException
     */
    #[Route('/', ['GET'], 'index.index')]
    public function index(Request $request): string
    {
        $posts_per_page = 10;
        $currentPage = intval($request->query->get('page', 1));

        // Todo: refactor negative and zero values for page var
        $offset = $currentPage === 1 ? 0 : $posts_per_page * ($currentPage - 1);
        $posts = $this->postRepository->getPosts($posts_per_page, $offset);

        if (!$posts)
            throw new ResourceNotFoundException();

        $totalItems = $this->postRepository->getCount();
        $paginator = new Paginator($totalItems, $posts_per_page, $currentPage, '?page=(:num)');

        return $this->view->index($posts, $paginator);
    }


    #[Route('/phpinfo', ['GET'], 'main.phpinfo', middlewares: [IsAdmin::class])]
    public function phpinfo(): string
    {
        ob_start();
        phpinfo();
        $info = ob_get_clean();

        return $this->view->phpinfo($info);
    }


    #[Route('/debug', ['GET', 'POST', 'PUT', 'DELETE'], 'main.debug')]
    public function debug(Request $request): string
    {
        return $this->toJson($request->request->all());
    }


    #[Route('/search', ['GET'], 'main.search')]
    public function search(Request $request)
    {
        throw  new Exception($request->query->get('q', 'none'));
    }
}