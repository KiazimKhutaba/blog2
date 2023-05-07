<?php

namespace MyBlog\Core;

class RequestBag
{
    private array $data = [];

    /**
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function has(string $key): bool
    {
        return !empty($this->data[$key]);
    }

    /**
     * @return array
     */
    public function get(string $key, $default = ''): mixed
    {
        return $this->data[$key] ?? $default;
    }


    public function toArray(): array
    {
        return $this->data;
    }
}