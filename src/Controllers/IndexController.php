<?php

namespace MyBlog\Controllers;

use JasonGrimes\Paginator;
use MyBlog\Core\Db\DatabaseInterface;
use MyBlog\Core\Traits\ToJsonStringTrait;
use MyBlog\Exceptions\ResourceNotFoundException;
use MyBlog\Repositories\PostRepository;
use MyBlog\Repositories\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class IndexController extends BaseController
{
    use ToJsonStringTrait;

    public function __construct
    (
        private readonly PostRepository $postRepository
    )
    {
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function index(Request $request): bool|string
    {
        $posts_per_page = 5;
        $currentPage = intval($request->query->get('page', 1));
        // Todo: refactor negative and zero values for page var
        $offset = $currentPage === 1 ? 0 : $posts_per_page * ($currentPage - 1);

        $posts = $this->postRepository->getPosts($posts_per_page, $offset);

        if(!$posts)
            throw new ResourceNotFoundException();

        $totalItems = $this->postRepository->getCount();
        //$pages = floor( $totalItems / $posts_per_page);

        $paginator = new Paginator($totalItems, $posts_per_page, $currentPage, '?page=(:num)');

        // Todo: not suit for many pages
        return $this->render('index/index.html.twig', ['posts' => $posts, 'paginator' => $paginator]);
    }


    public function phpinfo(): string
    {
        ob_start();
        phpinfo();
        $info = ob_get_clean();

        return $this->render('index/phpinfo.html.twig', ['info' => $info]);
    }


    public function debug(Request $request): string
    {
        return $this->toJson($request->request->all());
    }
}