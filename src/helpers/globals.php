<?php

use phpseclib3\Exception\FileNotFoundException;

function config($key)
{
    $keys = explode('.', $key);
    $path = __DIR__ . '/../../config/' . $keys[0] . '.php';
    if (!file_exists($path)) {
        throw new FileNotFoundException("{$path} not found");
    }
    $configs = require $path;
    array_shift($keys);
    foreach ($keys as $key) {
        if (array_key_exists($key, $configs)) {
            $configs = $configs[$key];
        } else {
            throw new OutOfBoundsException("{$key} not found in {$path}");
        }
    }
    return $configs ?? null;
}

function last(array $arr)
{
    return $arr[array_key_last($arr)];
}

function first(array $arr)
{
    return $arr[array_key_first($arr)];
}
