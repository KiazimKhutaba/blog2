<?php

namespace MyBlog\Plugins\Commentor\src\Repositories;

use MyBlog\Core\Db\DatabaseInterface;
use MyBlog\Plugins\Commentor\src\Dtos\NewCommentRequestDto;

class CommentsRepository
{
    public function __construct(
        private readonly DatabaseInterface $db
    )
    {
    }


    public function create(NewCommentRequestDto $comment)
    {
        return $this->db->insert($comment->toArray());
    }
}