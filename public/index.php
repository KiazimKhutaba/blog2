<?php


use Dotenv\Dotenv;
use MyBlog\Middlewares\CachingMiddleware;
use MyBlog\Application;
use MyBlog\Middlewares\AppVersion;
use MyBlog\Middlewares\ExecutionTime;
use Symfony\Component\HttpFoundation\Request;

require_once __DIR__ . '/../vendor/autoload.php';


// load environment vars
$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();


$app = new Application();
$request = Request::createFromGlobals();

$app->add([
    //CheckAuth::class,
    AppVersion::class,
    ExecutionTime::class,
    CachingMiddleware::class,

]);

$response = $app->process($request);

/**
 * Todo: middlewares work but headers not redefined, their value duplicated
 *
 */
$response->send();