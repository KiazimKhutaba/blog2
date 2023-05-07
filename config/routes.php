<?php

use Middlewares\RedirectIfAuthenticated;
use MyBlog\Controllers\AccountController;
use MyBlog\Controllers\IndexController;
use MyBlog\Controllers\PostController;
use MyBlog\Core\Routing\Route;
use MyBlog\Middlewares\IsAdmin;


return [
    new Route('/', [IndexController::class, 'index'], ['GET'], 'index.index'),

    // Post
    new Route('/post', [PostController::class, 'index'], ['GET', 'POST'], 'post.index', [], [IsAdmin::class]),
    new Route('/post/{id}', [PostController::class, 'show'], ['GET'], 'post.show', ['id' => '[\d]+']),
    new Route('/post/{id}/edit', [PostController::class, 'edit'], ['GET', 'POST'], 'post.edit', ['id' => '[\d]+'], [IsAdmin::class]),
    new Route('/post/{id}/remove', [PostController::class, 'remove'], ['DELETE'], 'post.remove', ['id' => '[\d]+'], [IsAdmin::class]),

    // Account
    new Route('/login', [AccountController::class, 'login'], ['GET', 'POST'], 'account.login', [RedirectIfAuthenticated::class]),
    new Route('/logout', [AccountController::class, 'logout'], ['GET'], 'account.logout'),
    new Route('/registration', [AccountController::class, 'registration'], ['GET', 'POST'], 'account.create', [RedirectIfAuthenticated::class]),
    new Route('/profile', [AccountController::class, 'index'], ['GET'], 'account.index'),

    new Route('/users/{id}/posts', [PostController::class, 'getUserPosts'], ['GET'], 'user.posts', ['id' => '[\d]+']),
    new Route('/users/posts', [PostController::class, 'getUsersPosts'], ['GET'], 'users.posts')
];

