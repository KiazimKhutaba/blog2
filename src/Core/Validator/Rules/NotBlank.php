<?php

namespace MyBlog\Core\Validator\Rules;


class NotBlank extends Rule
{
    public function __invoke(string $value, string $field): bool|string
    {
        return !empty(trim($value)) ?: "Field '$field' can't be empty";
    }
}