<?php

namespace MyBlog\Core\Validator\Rules;

class MaxLength extends Rule
{
    public function __construct
    (
        private readonly int $length
    )
    {}

    function __invoke(string $value, string $field): bool|string
    {
        return mb_strlen(trim($value)) <= $this->length ?: "Field '$field' length should be $this->length chars or less";
    }
}