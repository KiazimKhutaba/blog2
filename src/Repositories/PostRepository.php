<?php

namespace MyBlog\Repositories;

use MyBlog\Core\Db\DatabaseInterface;
use MyBlog\Core\Utils;
use MyBlog\Dtos\PostRequestDto;
use MyBlog\Models\Post;

class PostRepository
{
    /**
     * @param DatabaseInterface $db
     */
    public function __construct
    (
        private readonly DatabaseInterface $db
    )
    {
        $this->db->table("posts");
    }


    public function create(PostRequestDto $post): int
    {
        return $this->db->insert($post->toArray());
    }


    public function update(int $id, PostRequestDto $post)
    {
        return $this->db->update($id, $post->toArray());
    }


    public function getAll(int $page): array
    {
        $sql = "SELECT * FROM posts ORDER BY created_at DESC LIMIT 0, :page";
        $posts = $this->db->query($sql, [':page' => $page]);

        return array_map(static function (array $post) {
            $post['created_at'] = Utils::formatDatetime($post['created_at']);
            $post['content'] = substr($post['content'], 0, 100);
            return $post;
        }, $posts);
    }


    public function get(int $id): array
    {
        return $this->db->get($id) ?: [];
    }


    public function remove(int $post_id)
    {
        return $this->db->delete($post_id);
    }


    public function getUserPosts(int $user_id): array
    {
        $sql = "SELECT * FROM posts WHERE user_id = :user_id";

        return $this->db->query($sql, [':user_id' => $user_id], static function (array $posts) {
            $list = [];
            foreach ($posts as $post) $list[] = new Post(...$post);
            return $list;
        });
    }


    public function getUsersPosts()
    {
        $sql = "select
                    posts.title,
                    u.email
                from posts left join users u on posts.user_id = u.id
                where length(posts.title) > 0";

        return $this->db->query($sql, []);
    }
}