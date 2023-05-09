<?php

namespace Core\Validator\Rules;

use MyBlog\Core\Validator\Rules\Rule;

class NotEqual
{
    public function __construct
    (
        private readonly mixed $value
    )
    {
    }

    public function __invoke(mixed $value, string $field): bool|string
    {
        return $value !== $this->value ?: "Field '$field' value '$value' not equal $this->value";
    }
}