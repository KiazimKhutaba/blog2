<?php

namespace MyBlog\Models;


class User
{
    public int $id;
    public string $email;
    public string $password;
    public string $role;
    public string $created_at;
    public ?string $updated_at;

    public function __construct(
        int $id = 0,
        string $email = '',
        string $password = '',
        string $role = '',
        string $created_at = '',
        ?string $updated_at = '',
    )
    {
        $this->id = $id;
        $this->email = $email;
        $this->password = $password;
        $this->role = $role;
        $this->created_at = $created_at;
        $this->updated_at = $updated_at;
    }

}