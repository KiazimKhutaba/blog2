<?php

namespace MyBlog\Core\Db;

interface IDatabase 
{
    function select(string $sql, array $params, string $class);

    public function insert(string $sql, array $params);
}