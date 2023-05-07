<?php

namespace MyBlog\Dtos;

use MyBlog\Core\Validator\Rules\Email;
use MyBlog\Core\Validator\Rules\MinLength;
use MyBlog\Core\Validator\Rules\NotBlank;
use MyBlog\Core\Validator\Rules\SameIs;

class NewAccountRequestDto implements RequestDtoInterface
{
    private function __construct
    (
        public readonly string $email,
        public readonly string $password,
        public readonly string $password_repeat
    )
    {}

    public static function from(array $input): self
    {
        return new NewAccountRequestDto(
            $input['email'] ?? '', $input['password'] ?? '', $input['password_repeat'] ?? ''
        );
    }


    public function rules(): array
    {
        return [
            'email' => [new NotBlank(), new Email()],
            'password' => [new NotBlank(), new MinLength(5)],
            'password_repeat' => [new SameIs($this->password)]
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