<?php

use MyBlog\Middlewares\IsAuthorized;
use MyBlog\Middlewares\RedirectIfAuthenticated;
use MyBlog\Controllers\AccountController;
use MyBlog\Controllers\CommentsController;
use MyBlog\Controllers\IndexController;
use MyBlog\Controllers\PostController;
use MyBlog\Core\Routing\Route;
use MyBlog\Middlewares\IsAdmin;
use MyBlog\Middlewares\IsAuthenticated;


return [
    // Main
    new Route('/', [IndexController::class, 'index'], ['GET'], 'index.index'),
    new Route('/phpinfo', [IndexController::class, 'phpinfo'], ['GET'], 'main.phpinfo', middlewares: [IsAdmin::class]),
    new Route('/debug', [IndexController::class, 'debug'], ['GET', 'POST', 'PUT', 'DELETE'], 'main.debug'),

    // Post
    new Route('/post', [PostController::class, 'index'], ['GET', 'POST'], 'post.index', [], [IsAdmin::class]),
    new Route('/post/{id}', [PostController::class, 'show'], ['GET'], 'post.show', ['id' => '[\d]+']),
    new Route('/post/{id}/edit', [PostController::class, 'edit'], ['GET', 'POST'], 'post.edit', ['id' => '[\d]+'], [IsAdmin::class]),
    new Route('/post/{id}/remove', [PostController::class, 'remove'], ['GET'], 'post.remove', ['id' => '[\d]+'], [IsAdmin::class]),

    // Post comments
    new Route('/post/{id}/comment', [PostController::class, 'addComment'], ['POST'], 'post_comment.create', ['id' => '[\d]+'], [IsAuthenticated::class]),
    new Route('/comment/{id}/remove', [CommentsController::class, 'remove'], ['DELETE'], 'comment.remove', ['id' => '[\d]+'], [
        IsAuthenticated::class//, IsAuthorized::class => ['object_author_id']
    ]),

    // Account
    new Route('/login', [AccountController::class, 'login'], ['GET', 'POST'], 'account.login', [], [RedirectIfAuthenticated::class]),
    new Route('/logout', [AccountController::class, 'logout'], ['GET'], 'account.logout'),
    new Route('/registration', [AccountController::class, 'registration'], ['GET', 'POST'], 'account.create', [], [RedirectIfAuthenticated::class]),
    new Route('/profile', [AccountController::class, 'index'], ['GET'], 'account.index'),

    // Comment
    // new Route('/post/{id}/comments', [CommentsController::class, 'getAll'], ['GET'], 'post.comments', ['id' => '[\d]+']),

    new Route('/users/{id}/posts', [PostController::class, 'getUserPosts'], ['GET'], 'user.posts', ['id' => '[\d]+']),
    new Route('/users/posts', [PostController::class, 'getUsersPosts'], ['GET'], 'users.posts')
];

