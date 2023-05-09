<?php

namespace MyBlog\Controllers;

use MyBlog\Core\Container;
use MyBlog\Core\Routing\Router;
use MyBlog\Core\Session\SessionInterface;
use MyBlog\Core\Utils;
use MyBlog\Exceptions\ForbiddenException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class BaseController
{
    private readonly Container $container;

    public function setContainer(Container $container): void
    {
        $this->container = $container;
    }


    public function isUserLogged(): bool
    {
        /** @var SessionInterface $session */
        $session = $this->container->get(SessionInterface::class);
        return $session->has('user_id');
    }


    /**
     * @throws SyntaxError
     * @throws \ReflectionException
     * @throws RuntimeError
     * @throws LoaderError
     */
    protected function render(string $template, array $vars = []): string
    {
        /** @var Environment $templating */
        $templating = $this->container->get(Environment::class);
        return $templating->render($template, $vars);
    }


    public function redirect(string $location): RedirectResponse
    {
        return new RedirectResponse($location);
    }


    protected function redirectToRoute(string $name, array $values = []): RedirectResponse
    {
        /** @var Router $router */
        $router = $this->container->get(Router::class);
        $url = $router->generateUrl($name, $values);
        return new RedirectResponse($url);
    }


    protected function authorize(int $object_owner_id): void
    {
        /** @var SessionInterface $session */
        $session = $this->container->get(SessionInterface::class);

        $user_id = $session->get('user_id');
        $is_admin = $session->get('role') === 'admin';

        if($is_admin || ($user_id === $object_owner_id)) {
            return;
        }

        throw new ForbiddenException();
    }

}