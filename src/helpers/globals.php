<?php

/**
 * Global helper functions
 *
 * @package App
 * @author  Kareem Mohamed <kareemshaaban221@gmail.com>
 */

use App\Exceptions\IRenderable;
use phpseclib3\Exception\FileNotFoundException;

/**
 * Get a value from the config files
 *
 * @param string $key The key to retrieve the value from. If the key is nested, use the dot notation.
 * @return mixed The value of the key.
 * @throws FileNotFoundException If the config file is not found.
 * @throws OutOfBoundsException If the key is not found in the config file.
 */
function config(string $key)
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

/**
 * Get the last element of an array
 *
 * @param array $arr The array to get the last element from.
 * @return mixed The last element of the array.
 */
function last(array $arr)
{
    return $arr[array_key_last($arr)];
}

/**
 * Get the first element of an array
 *
 * @param array $arr The array to get the first element from.
 * @return mixed The first element of the array.
 */
function first(array $arr)
{
    return $arr[array_key_first($arr)];
}

/**
 * Render an IRenderable object as a string
 *
 * @param IRenderable $renderable The IRenderable object to render.
 * @return string The rendered string.
 */
function render(IRenderable $renderable)
{
    echo $renderable->render();
}
