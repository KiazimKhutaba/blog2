<?php

namespace MyBlog\Controllers;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class HttpErrorController extends BaseController
{
    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     * @throws \ReflectionException
     */
    public function error404(Request $request): Response
    {
        return new Response($this->render('404.html.twig', ['page' => $request->getRequestUri()]), 404);
    }


    public function error500(Request $request, \Throwable $exception): Response
    {
        $content = $this->render('500.html.twig', ['e' => $exception, 'trace' => $exception->getTraceAsString()]);
        return new Response($content, 500);
    }

    public function error403(Request $request): Response
    {
        $content = $this->render('403.html.twig', ['resource' => $request->getRequestUri()]);
        return new Response($content, 403);
    }
}