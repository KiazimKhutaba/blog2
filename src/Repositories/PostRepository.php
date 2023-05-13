<?php

namespace MyBlog\Repositories;

use MyBlog\Core\Db\DatabaseInterface;
use MyBlog\Core\Utils;
use MyBlog\Dtos\NewCommentRequestDto;
use MyBlog\Dtos\PostRequestDto;
use MyBlog\Models\Comment;
use MyBlog\Models\Post;

class PostRepository extends BaseRepository
{
    /**
     * @param DatabaseInterface $db
     */
    public function __construct
    (
        protected DatabaseInterface $db
    )
    {
        parent::__construct($db, 'posts');
    }


    public function update(int $id, PostRequestDto $post)
    {
        return $this->db->update($id, $post->toArray());
    }


    public function getPosts(int $limit): array
    {
        return $this->db->getAll($limit, 'posts', function (array $rows) {
            return array_map(static function (array $post) {
                $post['created_at'] = Utils::formatDatetime($post['created_at']);
                $post['content'] = substr($post['content'], 0, 100);
                return $post;
            }, $rows);
        });
    }



    public function get2(int|string $id): array
    {
        //throw new \Exception($this->db->table);

        return $this->db->get($id) ?: [];
        /*$sql = 'SELECT p.*, c.* FROM posts p LEFT JOIN comments c on p.id = c.post_id WHERE p.id = :id';
        return $this->db->queryOne($sql, [':id' => $id], static function (array $row) {
            return $row;
        }) ?: [];*/
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


    public function getCount(): int
    {
        return $this->db->rowsCount('posts');
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