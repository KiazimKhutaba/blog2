<?php

namespace MyBlog\Dtos;

use Core\Validator\Rules\NotEqual;
use MyBlog\Core\Validator\Rules\MinLength;
use MyBlog\Core\Validator\Rules\NotBlank;
use MyBlog\Dtos\RequestDtoInterface;

class NewCommentRequestDto implements RequestDtoInterface
{
    public function __construct
    (
        public readonly string $post_id,
        public readonly string $parent_id,
        public readonly string $content,
        public readonly int $reply_id = 0
    )
    {
    }

    public static function from(array $input): self
    {
        return new NewCommentRequestDto(
            $input['post_id'] ?? 0,
            intval($input['parent_id']) ?? 0,
            $input['content'] ?? '',
            $input['reply_id'] ?? 0,
        );
    }

    public function rules(): array
    {
        return [
            'post_id' => [new NotEqual(0)],
            /*'user_id' => [],*/
            'parent_id' => [],
            'content' => [new NotBlank(), new MinLength(10)],
            'reply_id' => [],
        ];
    }


    public function toArray(): array
    {
        return [
            'post_id' => intval($this->post_id),
            /*'user_id' => $this->user_id,*/
            'parent_id' => intval($this->parent_id),
            'content' => $this->content,
            'reply_id' => intval($this->reply_id),
        ];
    }
}