<?php

namespace MyBlog\Middlewares;

use Closure;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AppVersion //implements MiddlewareInterface
{

    /**
     * @inheritDoc
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        /*if(!$request->hasHeader('X-App'))
            throw new \Exception('Bad request');*/

        $response = $handler->handle($request);
        $response->withHeader('X-App-Version', 1);

        return $response;
    }


    /**
     * @param Request $request
     * @param Closure(Request): Response  $next
     * @return string
     */
    public function __invoke(Request $request, Closure $next): Response
    {
        $response = $next($request);
        $response->headers->set('X-App-Version', '1.0.1');
        $response->headers->set('X-Powered-By', 'https://github.com/kiazimkhutaba/blog2');

        return $response;
    }
}