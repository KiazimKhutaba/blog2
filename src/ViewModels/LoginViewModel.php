<?php

namespace MyBlog\ViewModels;

class LoginViewModel
{
    private string $redirect_url;


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
        return 'account/index.html.twig';
    }

    public function toArray(): array
    {
        return [
            'message' => $this->message,
            'errors' => $this->errors,
            'redirect_url' => $this->redirect_url,
        ];
    }

    /**
     * @return string
     */
    public function getRedirectUrl(): string
    {
        return $this->redirect_url;
    }

    /**
     * @param string $redirect_url
     */
    public function setRedirectUrl(string $redirect_url): void
    {
        $this->redirect_url = $redirect_url;
    }
}