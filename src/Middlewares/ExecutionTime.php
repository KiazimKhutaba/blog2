<?php

namespace MyBlog\Middlewares;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ExecutionTime implements MiddlewareInterface
{

    /**
     * {@inheritDoc}
     */
    public function __invoke(Request $request, \Closure $next): Response
    {
        $start = microtime(true);
        $response = $next($request);

        $end = sprintf('%.4f sec.', microtime(true) - $start);
        $response->headers->set('X-Time-Consumed', $end);

        return $response;
    }
}