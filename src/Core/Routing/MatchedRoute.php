<?php

namespace MyBlog\Core\Routing;


class MatchedRoute extends Route
{
    public array $routeParams = [];


    public function __construct(Route $route, array $params)
    {
        $this->url = $route->url;
        $this->name = $route->name;
        $this->handler = $route->handler;
        $this->methods = $route->methods;
        $this->routeParams = $params;
    }


    public function hasParams(): bool
    {
        return count($this->routeParams) > 0;
    }
}