<?php

namespace MyBlog\Core\Traits;

trait ToJsonStringTrait
{
    public function toJson(mixed $data): string
    {
        return json_encode($data ?: $this, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }
}