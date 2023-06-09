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
use Symfony\Component\HttpFoundation\Response;

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
    public function search(Request $request): string
    {
        $posts_per_page = 5;
        $current_page = intval($request->query->get('page', 1));

        // Todo: refactor negative and zero values for page var
        $offset = $current_page === 1 ? 0 : $posts_per_page * ($current_page - 1);

        $search_term = trim($request->query->get('q', ''));
        $found_posts = $this->postRepository->search($search_term, $offset, $posts_per_page);
        $total_items = count($found_posts);

        $paginator = new Paginator($total_items, $posts_per_page, $current_page, '&page=(:num)');

        return $this->view->search($found_posts, $paginator);
    }
}