<?php

namespace MyBlog\Repositories;

use MyBlog\Core\Db\DatabaseInterface;

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
}