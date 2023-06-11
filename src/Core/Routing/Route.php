<?php

namespace MyBlog\Core\Routing;

class Route
{
    private string $originalUrl;
    private array $attrs; // contains matched route param values

    public function __construct
    (
        public string $url = "",
        public array  $handler = [],
        public array  $methods = [],
        public string $name = "",
        public array  $params = [],
        public array  $middlewares = []
    )
    {
        $this->originalUrl = substr($url, 0);
    }


    public static function from(array $route): self
    {
        return new Route(...$route);
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

    /**
     * @param string $originalUrl
     */
    public function setOriginalUrl(string $originalUrl): void
    {
        $this->originalUrl = $originalUrl;
    }

    public function dump(): string
    {
        $methods = $this->stringify($this->methods);
        $params = $this->stringify($this->params);
        $middlewares = $this->stringify($this->middlewares);

        return "
        [
            'url' => '$this->url',
            'handler' => ['{$this->handler[0]}', '{$this->handler[1]}'],
            'methods' => [$methods],
            'name' => '$this->name',
            'params' => [$params],
            'middlewares' => [$middlewares],
        ]";
    }

    private function stringify(array $list): string
    {
        $new_list = [];
        foreach ($list as $key => $item)
        {
            if (is_string($key)) {
                $new_list[] = "'$key' => '$item'";
            } else {
                $new_list[] = "'$item'";
            }
        }

        return join(',', $new_list);
    }

    public function __toString()
    {
        return json_encode($this, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
}