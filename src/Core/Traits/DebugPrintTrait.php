<?php

namespace MyBlog\Core\Traits;

use Symfony\Component\HttpFoundation\Response;

trait DebugPrintTrait
{
    use ToJsonStringTrait;

    public function print(mixed $value): void
    {
        print sprintf('<pre>%s</pre>', $value);
    }

    public function debugJson(mixed $value): Response
    {
        return new Response($this->toJson($value), headers: ['Content-Type' => 'application/json']);
    }
}