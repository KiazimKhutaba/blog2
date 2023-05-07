<?php

namespace Middlewares;

use MyBlog\Core\Routing\Router;
use MyBlog\Core\Session\SessionInterface;
use MyBlog\Middlewares\MiddlewareInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated implements MiddlewareInterface
{
    public function __construct
    (
        private readonly SessionInterface $session,
        private readonly Router $router
    )
    {
    }


    /**
     * @inheritDoc
     */
    public function __invoke(Request $request, \Closure $next): Response
    {
        if($this->userIsLogged()) {
            return $this->redirectToRoute('account.index');
        }

        return $next($request);
    }

    public function userIsLogged(): bool
    {
        return $this->session->has('user_id');
    }


    protected function redirectToRoute(string $name, array $values = []): RedirectResponse
    {
        return new RedirectResponse($this->router->generateUrl($name, $values));
    }
}