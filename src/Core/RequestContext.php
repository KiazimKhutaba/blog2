<?php

namespace MyBlog\Core;


class RequestContext
{
    public readonly string $url;
    public readonly string $method;
    private array $postVars;
    private array $getVars;

    public function __construct()
    {
        $this->url = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $this->method = strtolower($_SERVER['REQUEST_METHOD']);

        $this->postVars = $_POST;
        $this->getVars = $_GET;
    }

    public function doneWitPostMethod(): bool
    {
        return 'post' == $this->method;
    }

    public function doneWithGetMethod(): bool
    {
        return 'get' == $this->method;
    }

    public function isAjax(): bool
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest';
    }

    public function post(): RequestBag
    {
        return new RequestBag($this->postVars);
    }

    public function get(): RequestBag
    {
        return new RequestBag($this->getVars);
    }


}