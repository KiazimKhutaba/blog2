<?php

namespace MyBlog\Repositories;

use MyBlog\Core\Db\DatabaseInterface;
use MyBlog\Dtos\NewAccountRequestDto;
use MyBlog\Models\User;

class UserRepository extends BaseRepository
{
    public function __construct
    (
        protected DatabaseInterface $db
    ) 
    {
        parent::__construct($this->db, 'users');
    }


   /* public function create(NewAccountRequestDto $user)
    {
        return $this->db->insert($user->toArray());
    }


    public function get(int $id): bool|User
    {
        $result = $this->db->get($id);
        return new User(...$result);
    }*/

    public function getByEmail(string $email): bool|User
    {
        $sql = "SELECT * FROM users WHERE email = :email";

        return $this->db->queryOne(
            $sql,
            [':email' => $email],
            fn($user) => new User(...$user)
        );
    }

    public function findByCredentials(string $email, string $password): User
    {
        return $this->db->get(1);
    }
}