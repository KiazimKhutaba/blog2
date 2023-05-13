<?php

namespace MyBlog\Core\Db;

interface DatabaseInterface
{
    public function table(string $table);

    public function query(string $sql, array $params, \Closure $convertor = null, int $fetchMode = \PDO::FETCH_ASSOC);
    public function queryOne(string $sql, array $params, \Closure $convertor = null, int $fetchMode = \PDO::FETCH_ASSOC);
    public function queryEx(string $sql, array $params, \Closure $convertor = null, int $fetchMode = \PDO::FETCH_ASSOC);
    public function getAll(int $limit, string $table, \Closure $convertor = null);
    public function get(int|string $id, string $table): array;
    public function insert(array $data, string $table): int;
    public function update(int|string $id, array $data, string $table);
    public function delete(int|string $id, string $table);

    public function rowsCount(string $table): int;
}