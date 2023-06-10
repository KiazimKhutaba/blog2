<?php

namespace MyBlog\Controllers;

use JasonGrimes\Paginator;
use MyBlog\Core\Db\DatabaseInterface;
use MyBlog\Core\Traits\ToJsonStringTrait;
use MyBlog\Exceptions\ResourceNotFoundException;
use MyBlog\Repositories\PostRepository;
use MyBlog\Repositories\UserRepository;
use MyBlog\ViewModels\IndexView;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class IndexController extends BaseController
{
    use ToJsonStringTrait;

    public function __construct
    (
        private readonly PostRepository $postRepository,
        private readonly IndexView $view
    )
    {
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function index(Request $request): string
    {
        $posts_per_page = 10;
        $currentPage = intval($request->query->get('page', 1));

        // Todo: refactor negative and zero values for page var
        $offset = $currentPage === 1 ? 0 : $posts_per_page * ($currentPage - 1);
        $posts = $this->postRepository->getPosts($posts_per_page, $offset);

        if(!$posts)
            throw new ResourceNotFoundException();

        $totalItems = $this->postRepository->getCount();
        $paginator = new Paginator($totalItems, $posts_per_page, $currentPage, '?page=(:num)');

        return $this->view->index($posts, $paginator);
    }


    public function phpinfo(): string
    {
        ob_start();
        phpinfo();
        $info = ob_get_clean();

        return $this->view->phpinfo($info);
    }


    public function debug(Request $request): string
    {
        return $this->toJson($request->request->all());
    }



    public function search(Request $request)
    {
        throw  new \Exception($request->query->get('q', 'none'));
    }
}