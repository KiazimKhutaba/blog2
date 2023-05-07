<?php

namespace MyBlog\Core\Validator\Rules;

abstract class Rule
{
    abstract public function __invoke(string $value, string $field): bool|string;
}