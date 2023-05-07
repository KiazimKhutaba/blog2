<?php

namespace MyBlog\ViewModels;

class PostViewModel
{

    /**
     * @param bool $is_edit
     * @param string $message
     * @param array $errors
     */
    public function __construct
    (
        public readonly bool $is_edit = false,
        public readonly string $message = "",
        public readonly array $errors = [],
        public readonly ?array $post = null
    )
    {
    }

    public function toArray(): array
    {
        return array_merge([
            'is_edit' => $this->is_edit,
            'message' => $this->message,
            'errors' => $this->errors,
        ], $this->post ?: []);
    }
}