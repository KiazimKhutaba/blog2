<?php

namespace MyBlog\Middlewares;

use Closure;
use MyBlog\Core\Routing\Router;
use MyBlog\Core\Session\SessionInterface;
use MyBlog\Exceptions\ForbiddenException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class IsAuthenticated implements MiddlewareInterface
{
    public function __construct
    (
        private readonly SessionInterface $session
    )
    {
    }

    /**
     * @inheritDoc
     * @throws ForbiddenException
     */
    public function __invoke(Request $request, Closure $next): Response
    {
        if(!$this->session->has('user_id')) {
            throw new ForbiddenException();
        }

        return $next($request);
    }
}