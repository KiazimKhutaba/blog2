<?php

namespace MyBlog\Repositories;

use JasonGrimes\Paginator;
use MyBlog\Core\Db\DatabaseInterface;
use MyBlog\Exceptions\ResourceNotFoundException;

class BaseRepository
{
    public function __construct
    (
        protected DatabaseInterface $db,
        protected string $table
    )
    {
    }

    public function add(array $obj): int
    {
        return $this->db->insert($obj, $this->table);
    }


    public function create(array $obj): int
    {
        return $this->db->insert($obj, $this->table);
    }


    public function get(int $id): array
    {
        return $this->db->get($id, $this->table) ?: [];
    }


    public function getAll(int $limit = 100): array
    {
        return $this->db->getAll($limit, $this->table);
    }


    public function remove(int|string $id)
    {
        return $this->db->delete($id, $this->table);
    }


    public function paginate(int $current_page,  int $posts_per_page, int $limit, int $offset = 0)
    {
        // Todo: refactor negative and zero values for page var
        /*$offset = $current_page === 1 ? 0 : $posts_per_page * ($current_page - 1);

        $sql = "SELECT * FROM $this->table ORDER BY `created_at` DESC LIMIT :offset, :limit";

        $rows = $this->postRepository->getPosts($posts_per_page, $offset);

        if(!$rows)
            throw new ResourceNotFoundException();

        $totalItems = $this->postRepository->getCount();
        return new Paginator($totalItems, $posts_per_page, $current_page, '?page=(:num)');*/
    }
}