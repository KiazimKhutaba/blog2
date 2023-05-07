<?php

namespace MyBlog\Core\Routing;


use MyBlog\Middlewares\MiddlewareInterface;

class Route
{
    public string $name;
    public string $url;
    public array $handler; // обработчик - метод класса
    public array $methods = [];
    // for middleware
    public array $middlewares  = [];
    public array $params = [];
    private string $originalUrl;


    public function __construct(string $url, array $handler, array $methods, string $name, array $params = [])
    {
        $this->name = $name;
        $this->url = $url;
        $this->originalUrl = substr($url, 0);
        $this->handler = $handler;
        $this->methods = $methods;
        $this->params = $params;
    }


    /**
     * @param list<string> $middleware
     * @return $this
     */
    public function middleware(array $middleware): self
    {
        $this->middlewares[] = array_merge($this->middlewares, $middleware);
        return $this;
    }

    public function __toString()
    {
        return json_encode($this, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    /**
     * @return string
     */
    public function getOriginalUrl(): string
    {
        return $this->originalUrl;
    }


    public static function middleware2()
    {

    }
}