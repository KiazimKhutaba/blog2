<?php

namespace MyBlog\Core\Db;

interface DatabaseInterface
{
    public function table(string $table);
    public function query(string $sql, array $params, \Closure $convertor = null, int $fetchMode = \PDO::FETCH_ASSOC);
    public function queryOne(string $sql, array $params, \Closure $convertor = null, int $fetchMode = \PDO::FETCH_ASSOC);
    public function getAll(int $limit);
    public function get($id);
    public function insert($data);
    public function update($id, $data);
    public function delete($id);
}