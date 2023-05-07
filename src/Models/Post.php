<?php

namespace MyBlog\Models;

class Post
{
    public function __construct
    (
        public readonly int $id,
        public readonly string $title,
        public readonly string $content,
        public readonly int $user_id,
        public readonly string $created_at,
    )
    {
    }
}