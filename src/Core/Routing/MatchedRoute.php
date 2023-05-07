<?php

namespace MyBlog\Core\Routing;


class MatchedRoute
{
    public array $routeParams = [];


    public function __construct
    (
        public readonly string $name,
        public readonly string $url,
        public readonly array $handler,
        public readonly array $methods,
        public readonly array $middlewares,
        public readonly array $params,
        public string $originalUrl,
    )
    {

    }


    public static function from(Route $route, array $route_params = [])
    {

    }

    public function hasMiddlewares(): bool
    {
        return count($this->middlewares) > 0;
    }

    public function hasParams(): bool
    {
        return count($this->routeParams) > 0;
    }
}