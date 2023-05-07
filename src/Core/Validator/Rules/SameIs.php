<?php

namespace MyBlog\Core\Validator\Rules;

class SameIs extends Rule
{
    public function __construct
    (
        private readonly string $compared
    )
    {
    }

    public function __invoke(string $value, string $field): bool|string
    {
        return $this->compared === $value ?: "Field '$field' value '$value' not equal to '$this->compared'";
    }
}