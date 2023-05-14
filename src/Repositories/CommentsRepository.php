<?php

namespace MyBlog\Repositories;

use MyBlog\Dtos\NewCommentRequestDto;
use MyBlog\Models\Comment;
use MyBlog\Core\Db\DatabaseInterface;
use function MyBlog\Helpers\buildTree;

class CommentsRepository extends BaseRepository
{
    public function __construct
    (
        protected DatabaseInterface $db
    )
    {
        parent::__construct($this->db, 'comments');
    }

    public function create(NewCommentRequestDto $dto, int $post_id, int $user_id): array
    {
        $data = [...$dto->toArray(), 'post_id' => $post_id, 'user_id' => $user_id];
        //return $data;

        $id = $this->db->insert($data, $this->table);

        if($id) {
            $sql = 'SELECT c.*, u.email as author FROM comments c LEFT JOIN users u on u.id = c.user_id WHERE c.id = :id';
            return $this->db->queryMany($sql, ['id' => $id])[0];
        }

        return [];
    }

    public function getComments(int $post_id)
    {
        $sql = 'SELECT c.*, u.email as author FROM comments c INNER JOIN users u on u.id = c.user_id WHERE post_id = :post_id';
        $rows = $this->db->query($sql, [':post_id' => $post_id]);

        if(!$rows) return [];

        return buildTree($rows, child_key: 'id');
    }




}