<?php

namespace MyBlog\ViewModels;

use JasonGrimes\Paginator;
use Twig\Environment;

class IndexView
{
    public function __construct(
        public readonly Environment $environment
    )
    {}

    public function index(array $posts, Paginator $paginator): string
    {
        return $this->environment->render('index/index.html.twig', ['posts' => $posts, 'paginator' => $paginator]);
    }


    public function phpinfo(string $info): string
    {
        return $this->environment->render('index/phpinfo.html.twig', ['info' => $info]);
    }
}