<?php

namespace MyBlog\Models;


class User
{
    public int $id;

    public string $email;
    public string $password;
    public string $role;

    public function __construct(int $id = 0, string $email = '', string $password = '', string $role = '')
    {
        $this->id = $id;
        $this->email = $email;
        $this->password = $password;
        $this->role = $role;
    }

}