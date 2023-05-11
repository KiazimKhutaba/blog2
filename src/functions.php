<?php

namespace MyBlog\Helpers;


function env(string $varname, $default = '')
{
    return !empty($_ENV[$varname]) ? $_ENV[$varname] : $default;
}

function isDev(): bool
{
    return env('APP_ENV') === 'dev';
}

function isProd(): bool
{
    return env('APP_ENV') === 'prod';
}

function map_keys(array $array, callable $callback): array
{
    $new_array = [];
    foreach ($array as $key => $value)
        $new_array[$callback($key)] = $value;

    return $new_array;
}


function buildTree(array &$list, string $parent_key = 'parent_id', string $child_key = 'comment_id')
{
    $tree = [];

    // делаем группировку по идентификатору родителя
    foreach ($list as $node) {
        $tree[$node[$parent_key]][] = $node;
    }

    // рекурсивная функция, создающая вложенную (древовидную) структуру
    $recursiveBuilder = function ($children) use (&$recursiveBuilder, $tree, $child_key) {

        foreach ($children as $key => $child)
        {
            $child_id = $child[$child_key];

            if (isset($tree[$child_id])) {
                $child['answers'] = $recursiveBuilder($tree[$child_id]);
            }

            $children[$key] = $child;
        }

        return $children;
    };

    return $recursiveBuilder($tree[0]);
}