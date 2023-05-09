<?php

namespace MyBlog\Models;

class Comment
{
    public function __construct
    (
        private readonly int $id,
        private readonly int $parent_id,
        private readonly int $reply_id,
        private readonly int $post_id,
        private readonly int $user_id,
        private readonly string $content,
        private readonly string $created_at
    )
    {
    }
}