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
    CachingMiddleware::class,
    AppVersion::class,
    ExecutionTime::class,
]);

$response = $app->process($request);

$response->send();