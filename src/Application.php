<?php

namespace MyBlog;

use Exception;
use MyBlog\Controllers\BaseController;
use MyBlog\Controllers\HttpErrorController;
use MyBlog\Core\Container;
use MyBlog\Core\Routing\Route;
use MyBlog\Core\Routing\Router;
use MyBlog\Exceptions\ForbiddenException;
use MyBlog\Exceptions\ResourceNotFoundException;
use MyBlog\Middlewares\MiddlewareInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class Application
{
    private array $middlewares = [];

    private readonly Container $container;

    public function __construct()
    {
        // global exception handler
        // set_exception_handler([$this, 'appExceptionHandler']);

        $this->container = $this->initServices();

        $this->initRoutes();
    }

    //use DebugPrintTrait;

    public function initRoutes(): void
    {
        /** @var list<Route> $routes */
        $routes = require_once __DIR__ . '/../config/routes.php';

        /** @var Router $router */
        $router = $this->container->get(Router::class);

        foreach ($routes as $route)
            $router->addRoute($route);

        //$this->print($router->getRoutes());
    }

    public function initServices(): Container
    {
        $container = new Container();

        $services = require(__DIR__ . '/../config/services.php');
        return $services($container);
    }


    /**
     * @param array $middlewares
     * @return void
     */
    public function add(array $middlewares): void
    {
        foreach ($middlewares as $middleware)
            $this->middlewares[] = $middleware;
    }



    public function process(Request $request): Response
    {
        try {

            $action = fn(Request $request): Response => $this->handle($request);

            foreach ($this->middlewares as $middleware) {
                $middlewareObj = $this->container->get($middleware);
                $action = fn(Request $request): Response => $middlewareObj($request, $action);
            }

            $response = $action($request);
        }
        catch (\Throwable|\Error $e)
        {
            /** @var HttpErrorController $error */
            $error = $this->container->get(HttpErrorController::class);
            $error->setContainer($this->container);

            if ($e instanceof ResourceNotFoundException) {
                $response = $error->error404($request);
            }
            else if($e instanceof ForbiddenException) {
                $response = $error->error403($request);
            } else {
                $response = $error->error500($request, $e);
            }
        }

        return $response;
    }

    /**
     * @throws SyntaxError
     * @throws \ReflectionException
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function appExceptionHandler(\Throwable $e): void
    {
        print $e->getMessage();
    }


    /**
     * @throws Exception
     */
    public function handle(Request $request): Response
    {

        $matchedRoute = $this->container->get(Router::class)->match($request);
        [$controller, $method] = $matchedRoute->handler;


        /** @var BaseController $obj */
        $obj = $this->container->get($controller); // magic happens here :)
        $obj->setContainer($this->container);

        //$methodRef = new \ReflectionMethod($obj, $method);

        $response = call_user_func_array([$obj, $method], [$request, ...$matchedRoute->routeParams]);

        return $response instanceof Response ? $response : new Response($response);
    }



}