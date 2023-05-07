<?php

namespace MyBlog\ViewModels;

class RegistrationViewModel
{
    public function __construct
    (
        public readonly string $message = '',
        public array $errors = []
    )
    {
    }

    public function addError(string $message): self
    {
        $this->errors[] = $message;
        return $this;
    }


    public function setErrors(array $errors): void
    {
        $this->errors = $errors;
    }

    public function getViewName(): string
    {
        return 'account/registration.html.twig';
    }

    public function toArray(): array
    {
        return [
            'message' => $this->message,
            'errors' => $this->errors,
        ];
    }
}