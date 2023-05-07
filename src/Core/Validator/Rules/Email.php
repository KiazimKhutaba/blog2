<?php

namespace MyBlog\Core\Validator\Rules;

class Email extends Rule
{
    public function __invoke(string $value, string $field): bool|string
    {
        return filter_var(trim($value), FILTER_VALIDATE_EMAIL) ? true : "Field '$field' is not correct email address";
    }
}