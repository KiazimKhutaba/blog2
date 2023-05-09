<?php

namespace MyBlog\Middlewares;

use MyBlog\Core\Session\SessionInterface;
use MyBlog\Exceptions\ForbiddenException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class IsAuthorized implements MiddlewareInterface
{

    public function __construct
    (
        private readonly SessionInterface $session,
        private readonly int $object_author_id
    )
    {
    }

    /**
     * @inheritDoc
     */
    public function __invoke(Request $request, \Closure $next): Response
    {
        $user_id = $this->session->get('user_id');
        $is_admin = $this->session->get('role') === 'admin';

        if($is_admin || ($user_id === $this->object_author_id)) {
            return $next($request);
        }

        throw new ForbiddenException();
    }
}