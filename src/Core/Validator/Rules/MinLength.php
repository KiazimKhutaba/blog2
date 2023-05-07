<?php

namespace MyBlog\Core\Validator\Rules;

class MinLength extends Rule
{
    public function __construct
    (
        private int $minLength
    )
    {}

    function __invoke(string $value, string $field): bool|string
    {
        return mb_strlen(trim($value)) >= $this->minLength ?: "Field '$field' length should be $this->minLength chars or greater";
    }
}