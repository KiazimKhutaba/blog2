<?php

namespace MyBlog\Middlewares;


use MyBlog\Core\Routing\Router;
use MyBlog\Exceptions\ForbiddenException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CachingMiddleware implements MiddlewareInterface
{

    public function __construct
    (
        private readonly Router $router
    )
    {
    }

    public function __invoke(Request $request, \Closure $next): Response
    {
        /** @var Response $response */
        $response = $next($request);

        $route = $this->router->match($request);

        if(\in_array($route->name, ['post.show'])) {
            throw new \Exception(CachingMiddleware::class);
            $response->headers->replace(['cache-control' => 'max-age=3600, must-revalidate, public']);
        }

        // cache publicly for 3600 seconds
        /*$response->setPublic();
        $response->setMaxAge(3600);

        // (optional) set a custom Cache-Control directive
        $response->headers->addCacheControlDirective('must-revalidate', true);*/

        return $response;
    }
}