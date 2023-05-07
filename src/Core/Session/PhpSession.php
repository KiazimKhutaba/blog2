<?php

namespace MyBlog\Core\Session;

use MyBlog\Core\Session\SessionInterface;

class PhpSession implements SessionInterface
{
    public function __construct(?int $cache_expire = null, ?string $cache_limiter = null)
    {
        if (PHP_SESSION_NONE === session_status())
        {
            if ($cache_limiter !== null) {
                session_cache_limiter($cache_limiter);
            }

            if ($cache_expire !== null) {
                session_cache_expire($cache_expire);
            }

            session_start();
        }
    }


    public function get(string $key): mixed
    {
        if ($this->has($key)) {
            return $_SESSION[$key];
        }
        return null;
    }


    public function set(string $key, mixed $value): self
    {
        $_SESSION[$key] = $value;
        return $this;
    }

    public function remove(string $key): void
    {
        if ($this->has($key)) {
            unset($_SESSION[$key]);
        }
    }

    public function clear(): void
    {
        //$_SESSION = [];
        session_unset();
    }

    public function has(string $key): bool
    {
        return array_key_exists($key, $_SESSION);
    }
}