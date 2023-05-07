<?php

namespace MyBlog\Plugins\Commentor\src\Dtos;

use MyBlog\Dtos\RequestDtoInterface;

class NewCommentRequestDto implements RequestDtoInterface
{
    public function __construct
    (
        public readonly string $name,
        public readonly string $email,
        public readonly string $content,
        public readonly ?int $reply_to
    )
    {
    }

    public static function from(array $input): self
    {
        return new NewCommentRequestDto(
            $input['name'] ?? '',
            $input['email'] ?? '',
            $input['content'] ?? '',
            $input['reply_to'] ?? 0
        );
    }

    public function rules(): array
    {
        return [
          'name' => [],
          'email' => [],
          'content' => [],
          'reply_to' => []
        ];
    }


    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'content' => $this->content,
            'reply_to' => $this->reply_to
        ];
    }
}