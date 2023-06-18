<?php

namespace MyBlog\Repositories;

use MyBlog\Core\Db\DatabaseInterface;
use MyBlog\Core\Utils;
use MyBlog\Dtos\PostRequestDto;
use MyBlog\Models\Post;
use function MyBlog\Helpers\debug;

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
        return $this->db->update($id, $post->toArray(), 'posts');
    }

    public function getPosts(int $limit, int $offset = 0): array|bool
    {
        $convertor = $this->postConvertor(...);

        $sql = 'SELECT posts.*, sc.views_count as vc
                FROM posts 
                LEFT JOIN stat_counter sc on posts.id = sc.post_id
                ORDER BY posts.created_at DESC LIMIT :offset, :limit';

        //debug(strtr($sql, [':offset' => $offset, ':limit' => $limit]));

        return $this->db->queryMany($sql, [':offset' => $offset, ':limit' => $limit], $convertor);
    }


    public function search(string $search_term, int $offset = 0, int $limit = 10): array
    {
        $sql = 'SELECT posts.*, sc.views_count as vc
                FROM posts 
                LEFT JOIN stat_counter sc on posts.id = sc.post_id
                WHERE title LIKE :search_term OR content LIKE :search_term 
                ORDER BY created_at DESC LIMIT :offset, :limit';

        $rows = $this->db->queryMany($sql, [
            ':search_term' => "%$search_term%",
            ':offset' => $offset,
            ':limit' => $limit
        ], $this->postConvertor(...));

        if(!$rows) return [];

        return $rows;
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



    private function postConvertor(array $post): array
    {
        $_post = [];

        $_post['id'] = $post['id'];
        $_post['title'] = sprintf('#%d %s', $post['id'], $post['title']);
        $_post['created_at'] = Utils::formatDatetime($post['created_at']);
        $_post['content'] = substr($post['content'], 0, 100);
        $_post['vc'] = $post['vc'] ?? 0;
        return $_post;
    }
}