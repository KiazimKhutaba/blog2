<?php declare(strict_types=1);

namespace MyBlog\Core;

use ReflectionException;

/**
 * @see https://habr.com/ru/articles/655399/
 */
class Container
{
    private array $services = [];


    public function set(string $id, callable $builder): void
    {
        $this->services[$id] = $builder;
    }

    public function has(string $id): bool
    {
        return isset($this->services[$id]) || class_exists($id);
    }

    /**
     * @throws ReflectionException
     */
    public function get(string $id, array $params = []): mixed
    {
        return isset($this->services[$id]) ? $this->services[$id]() : $this->prepareObject($id);
    }


    /**
     * @throws ReflectionException
     */
    public function getMethodParams(string $obj, string $method): array
    {
        $types = ['int', 'bool', 'float', 'string', 'array', 'object', 'callable', 'iterable', 'resource', 'null'];
        $method = new \ReflectionMethod($obj, $method);
        $methodArguments = $method->getParameters();
        $args = [];

        if(empty($methodArguments))
            return $args;

        foreach ($methodArguments as $argument)
        {
            $argumentType = $argument->getType()->getName();

            if(in_array($argument->getName(), $types)) {
                $args[$argument->getName()] = $argument->isOptional() ? $argument->getDefaultValue() : null;
            }
            else {
                //throw new \RuntimeException($argument->getType()->getName());
                $args[$argument->getName()] = $this->get($argumentType);
            }
        }

        return $args;
    }

    /**
     * @throws ReflectionException
     */
    private function prepareObject(string $class): ?object
    {
        $types = ['int', 'bool', 'float', 'string', 'array', 'object', 'callable', 'iterable', 'resource', 'null'];

        if(in_array($class, $types)) return null;

        $classReflector = new \ReflectionClass($class);

        $constructReflector = $classReflector->getConstructor();
        if (empty($constructReflector)) {
            return new $class;
        }

        $constructArguments = $constructReflector->getParameters();
        if (empty($constructArguments)) {
            return new $class;
        }


        $args = [];
        foreach ($constructArguments as $argument) {
            // Получаем тип аргумента
            $argumentType = $argument->getType()->getName();
            // Получаем сам аргумент по его типу из контейнера
            $args[$argument->getName()] = $this->get($argumentType);
        }

        // И возвращаем экземпляр класса со всеми зависимостями
        return new $class(...$args);
    }

}