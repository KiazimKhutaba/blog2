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
    public function get(string $id): mixed
    {
        return isset($this->services[$id]) ? $this->services[$id]() : $this->prepareObject($id);
    }

    /**
     * @throws ReflectionException
     */
    private function prepareObject(string $class): object
    {
        //if(!is_callable($class)) throw new \Exception("$class is not callable");

        $classReflector = new \ReflectionClass($class);

        // Получаем рефлектор конструктора класса, проверяем - есть ли конструктор
        // Если конструктора нет - сразу возвращаем экземпляр класса
        $constructReflector = $classReflector->getConstructor();
        if (empty($constructReflector)) {
            return new $class;
        }

        // Получаем рефлекторы аргументов конструктора
        // Если аргументов нет - сразу возвращаем экземпляр класса
        $constructArguments = $constructReflector->getParameters();
        if (empty($constructArguments)) {
            return new $class;
        }

        // Перебираем все аргументы конструктора, собираем их значения
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