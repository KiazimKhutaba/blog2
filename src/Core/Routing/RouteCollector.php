<?php

namespace MyBlog\Core\Routing;


use Exception;
use MyBlog\Core\Routing\Annotation\Route as RouteAttribute;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;

class RouteCollector
{
    public function dump(array $routes, string $file): void
    {
        $list = array_map(fn($route) => $route->dump(), $routes);
        $str = "<?php return [" . join(",", $list) . PHP_EOL . "];";

        file_put_contents($file, $str);
    }


    public function getDump(string $dump_file, array $classes): array
    {
        if(is_file($dump_file))
        {
            $routes_list = require_once $dump_file;
            return array_map([Route::class, 'from'], $routes_list);
        }

        $routes = $this->collectAll($classes);
        $this->dump($routes, $dump_file);

        return $routes;
    }

    /**
     * @param array $classes
     * @return array<Route>
     * @throws ReflectionException
     */
    public function collectAll(array $classes): array
    {
        $routes = [];
        foreach ($classes as $class) {
            $routes = array_merge($routes, $this->collect($class));
        }

        return $routes;
    }

    /**
     *
     * @return array<Route>
     * @throws ReflectionException
     */
    public function collect($class): array
    {
        $refClass = new ReflectionClass($class);

        $classAttributes =  $refClass->getAttributes(RouteAttribute::class);//[0] ?? false;

        if(count($classAttributes) > 1) {
            throw new Exception('Can\'t assign more than one RouteAttribute to class');
        }

        $refMethods = $refClass->getMethods(ReflectionMethod::IS_PUBLIC);

        $routes = [];
        foreach ($refMethods as $method)
        {
            $methodAttributes = $method->getAttributes(RouteAttribute::class);//[0] ?? false;

            if(count($methodAttributes) > 1) {
                throw new Exception('Can\'t assign more than one RouteAttribute to class method');
            }

            $classAttribute = isset($classAttributes[0]) ? $classAttributes[0]->newInstance() : null;
            $methodAttribute = isset($methodAttributes[0]) ? $methodAttributes[0]->newInstance() : null;

            $route = new Route();

            if($methodAttribute)
            {
                //throw new \Exception($methodAttribute);

                if ($classAttribute) {
                    $route->url = "{$classAttribute -> url}{$methodAttribute -> url}";
                    $route->methods = array_merge($classAttribute->methods, $methodAttribute->methods);

                } else {
                    $route->url = $methodAttribute->url;
                    $route->methods = $methodAttribute->methods;

                }

                $route->setOriginalUrl($route->url);
                $route->handler = [$class, $method->getName()];
                $route->name = $methodAttribute->name;
                $route->params = $methodAttribute->params;
                $route->middlewares = $methodAttribute->middlewares;

                $routes[] = $route;
            }

        }

        return $routes;
    }
}