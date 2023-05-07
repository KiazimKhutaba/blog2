<?php

namespace MyBlog\Dtos;

use MyBlog\Core\Validator\Rules\Email;
use MyBlog\Core\Validator\Rules\MinLength;
use MyBlog\Core\Validator\Rules\NotBlank;

class LoginRequestDto implements RequestDtoInterface
{

    private function __construct
    (
        public readonly string $email,
        public readonly string $password
    )
    {}

    public static function from(array $input): self
    {
        return new LoginRequestDto(
            $input['email'] ?? '', $input['password'] ?? ''
        );
    }


    public function rules(): array
    {
        return [
            'email' => [new NotBlank(), new Email()],
            'password' => [new NotBlank(), new MinLength(5)],
        ];
    }

    public function toArray(): array
    {
        return [
            'email' => $this->email,
            'password' => $this->password
        ];
    }
}