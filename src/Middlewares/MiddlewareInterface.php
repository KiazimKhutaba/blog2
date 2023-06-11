<?php

namespace MyBlog\Middlewares;

use Closure;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

interface MiddlewareInterface
{
    /**
     * @param Request $request
     * @param Closure(Request): Response $next
     * @return Response
     */
    public function __invoke(Request $request, Closure $next): Response;
}