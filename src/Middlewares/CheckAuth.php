<?php

namespace MyBlog\Middlewares;

use MyBlog\Core\Routing\Router;
use MyBlog\Core\Session\SessionInterface;
use MyBlog\Core\Traits\DebugPrintTrait;
use MyBlog\Exceptions\ForbiddenException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAuth implements MiddlewareInterface
{
    use DebugPrintTrait;


    public function __construct
    (
        private readonly Router $router,
        private readonly SessionInterface $session
    )
    {
    }


    /**
     * @throws \Exception
     */
    public function __invoke(Request $request, \Closure $next): Response
    {
        $matchedRoute = $this->router->match($request);

        $protected_route_names = [
            // post
            'post.index',
            'post.edit',
            'post.remove',

            // account
            //'account.index'
        ];


        $is_admin = $this->session->get('role') === 'admin';

        if(in_array($matchedRoute->name, $protected_route_names) && !$is_admin) {
            //return new Response('Forbidden', 401);
            throw new ForbiddenException();
        }

        return $next($request);
    }

}