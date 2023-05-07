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
