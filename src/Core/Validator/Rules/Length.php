<?php

namespace MyBlog\Core\Validator\Rules;

class Length extends Rule
{
    public function __construct
    (
        private readonly int $min,
        private readonly int $max
    )
    {}


    public function __invoke(string $value, string $field): bool
    {
        $trimmed = trim($value);

        return (mb_strlen($trimmed) >= $this->min) && (mb_strlen($trimmed) <= $this->max)
            ?: "Length of field '$field' should be between $this->min and $this->max chars";
    }
}