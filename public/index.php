<?php


use MyBlog\Application;
use MyBlog\Middlewares\AppVersion;
use MyBlog\Middlewares\IsAdmin;
use MyBlog\Middlewares\ExecutionTime;
use Dotenv\Dotenv;
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
    ExecutionTime::class
]);

$response = $app->process($request);
$response->send();