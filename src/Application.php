<?php

namespace MyBlog;

use Exception;
use InvalidArgumentException;
use MyBlog\Controllers\BaseController;
use MyBlog\Controllers\HttpErrorController;
use MyBlog\Core\Container;
use MyBlog\Core\Routing\Route;
use MyBlog\Core\Routing\Router;
use MyBlog\Core\Traits\ToJsonStringTrait;
use MyBlog\Exceptions\ForbiddenException;
use MyBlog\Exceptions\ResourceNotFoundException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use function MyBlog\Helpers\debug;

class Application
{
    use ToJsonStringTrait;

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


    /**
     * @throws \ReflectionException
     */
    private function pipeline(Request $request, callable $main, array $middlewares = []): Response
    {
        $action = fn(Request $request) => $main($request);

        if(count($middlewares) == 0) return $main($request);

        foreach ($middlewares as $middleware)
        {
            $middlewareObj = $this->container->get($middleware);
            $action = fn(Request $request): Response => $middlewareObj($request, $action);
        }

        //$action = fn(Request $request) => $main($request);
        //return $main($request, $action);

        $resp = $action($request);

        return $resp;
    }



    /**
     * @throws Exception
     */
    public function handle(Request $request): Response
    {

        /** @var Route $matchedRoute */
        $matchedRoute = $this->container->get(Router::class)->match($request);

        return $this->pipeline(
            $request,
            fn(Request $request) => $this->executeControllerAction($request, $matchedRoute),
            $matchedRoute->middlewares
        );
    }

    /**
     * @param Request $request
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws \ReflectionException
     */
    public function process(Request $request): Response
    {
        try {
            $response = $this->pipeline($request, [$this, 'handle'], $this->middlewares);
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

        if($request->isXmlHttpRequest())
            return new JsonResponse($response->getContent(), $response->getStatusCode(), [], true);

        return $response;
    }

    /**
     */
    public function appExceptionHandler(\Throwable $e): void
    {
        print $e->getMessage();
    }


    /**
     * @throws \ReflectionException
     */
    private function executeControllerAction(Request $request, Route $route): Response
    {

        [$controller, $method] = $route->handler;

        /** @var BaseController $obj */
        $obj = $this->container->get($controller); // magic happens here (DI and etc.) :)

        if(method_exists($obj, 'setContainer'))
            $obj->setContainer($this->container);

        $methodParams = $this->container->getMethodParams($controller, $method);
        $attrs = array_merge($methodParams, $route->getAttrs());

        //throw new Exception($this->toJson($attrs));

        $response = call_user_func_array([$obj, $method], $attrs);

        //return $response;
        // Todo: if return values should be JsonResponse or another
        return $response instanceof Response ? $response : new Response($response);
    }

}