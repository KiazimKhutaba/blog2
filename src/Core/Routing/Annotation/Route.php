<?php

namespace MyBlog\Core\Routing\Annotation;


use Attribute;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD)]
class Route
{
    public function __construct
    (
        public readonly string $url,
        public readonly array $methods,
        public readonly string $name = '',
        public readonly array $params = [],
        public readonly array $middlewares = [],
        public readonly array $attrs = [] // contains matched route param values
    )
    {

    }


    public function __toString()
    {
        return json_encode($this, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
}