<?php

namespace MyBlog\Core\Routing;

use MyBlog\Exceptions\ResourceNotFoundException;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use function MyBlog\Helpers\map;
use function MyBlog\Helpers\map_keys;

class Router
{
    /**
     * @var array<Route>
     */
    public array $routes = [];


    public function addRoutes(array $routes = [])
    {
        foreach ($routes as $route) {
            $this->addRoute($route);
        }
    }


    public function addRoute(Route $route): void
    {
        if(count($route->params) > 0)
        {
            foreach ($route->params as $name => $type)
            {
                $route->url = str_replace("{". $name ."}", "($type)", $route->url);
            }
        }

        $route->url = $this->createPattern($route->url);

        $this->routes[$route->name] = $route;
    }


    private function createPattern(string $input): string
    {
        return  '/^' . str_replace('/', '\/', $input) . '$/';
    }


    public function getRoute(string $name): Route
    {
        return $this->routes[$name] ?? throw new Exception("Route with name '$name' not found");
    }


    public function generateUrl(string $name, array $values): string
    {
        $translations = map_keys($values, function ($key) {
            return "{" . $key . "}";
        });

        return strtr($this->getRoute($name)->getOriginalUrl(), $translations);
    }

    public function getRoutes()
    {
        return $this->routes;
    }


    /**
     * @throws Exception
     */
    public function match(Request $request): MatchedRoute
    {
        //exit($request->getPathInfo());

        /** @var Route $route */
        foreach ($this->routes as $route) 
        {
            if (preg_match($route->url, $request->getPathInfo(), $params))
            {
                if(!in_array($request->getMethod(), $route->methods))
                    throw new Exception("Method '{$request->getMethod()}' not allowed for route '$route->name'");

                array_shift($params);

                return new MatchedRoute($route, $params);
            }
        }

        throw new ResourceNotFoundException("Resource not found");
    }
}