<?php

namespace MyBlog\ViewModels;

use Twig\Environment;

class RegistrationView
{
    public function __construct(
        private readonly Environment $environment
    )
    {
    }

    public function userExistsError()
    {
        return $this->environment->render('account/registration.html.twig', ['req' => [

        ]]);
    }
}