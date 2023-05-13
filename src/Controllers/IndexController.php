<?php

namespace MyBlog\Controllers;

use MyBlog\Core\Db\DatabaseInterface;
use MyBlog\Core\Traits\ToJsonStringTrait;
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
        $limit = $request->query->get('page', 10);
        $posts = $this->postRepository->getPosts($limit);
        $pages = $this->postRepository->getCount() / 5;


        return $this->render('index/index.html.twig', ['posts' => $posts, 'pages' => $pages]);
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