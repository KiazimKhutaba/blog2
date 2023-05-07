<?php

namespace MyBlog\Dtos;

use MyBlog\Core\Traits\ToJsonStringTrait;
use MyBlog\Core\Validator\Rules\MinLength;
use MyBlog\Core\Validator\Rules\NotBlank;
use Exception;

class PostRequestDto implements RequestDtoInterface
{
    use ToJsonStringTrait;


    private function __construct
    (
        public readonly string $title,
        public readonly string $content
    )
    {}


    /**
     * @throws Exception
     */
    public static function from(array $input): self
    {
        return new self($input['title'] ?? '', $input['content'] ?? '');
    }


    public function rules(): array
    {
        return [
          'title' => [new NotBlank(), new MinLength(10)],
          'content' => [new NotBlank(), new MinLength(10)]
        ];
    }


    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'content' => $this->content
        ];
    }
}