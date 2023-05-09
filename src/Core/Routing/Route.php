<?php

namespace MyBlog\Core\Routing;


class Route
{
    private string $originalUrl;
    private array $attrs; // contains matched route param values


    public function __construct
    (
        public string $url,
        public array $handler,
        public array $methods,
        public string $name,
        public array $params = [],
        public array $middlewares = []
    )
    {
        $this->originalUrl = substr($url, 0);
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


    /**
     * @return string
     */
    public function getOriginalUrl(): string
    {
        return $this->originalUrl;
    }

    public function __toString()
    {
        return json_encode($this, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    /**
     * @return array
     */
    public function getAttrs(): array
    {
        return $this->attrs;
    }

    /**
     * @param array $attrs
     */
    public function setAttrs(array $attrs): void
    {
        $this->attrs = $attrs;
    }
}