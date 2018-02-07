<?php

namespace Digia\GraphQL\Util;

/**
 * @param string $className
 * @param mixed $instance
 * @return mixed
 */
function instantiateIfNecessary(string $className, $instance)
{
    return $instance instanceof $className ? $instance : new $className($instance);
}

/**
 * @param bool   $condition
 * @param string $message
 * @throws \Exception
 */
function invariant(bool $condition, string $message)
{
    if (!$condition) {
        throw new \Exception($message);
    }
}

/**
 * @param      $array
 * @param      $key
 * @param null $defaultValue
 * @return null
 */
function arrayGet($array, $key, $defaultValue = null)
{
    return $array[$key] ?? $defaultValue;
}

/**
 * @param $value
 * @return string
 */
function toString($value): string
{
    return is_array($value) ? json_encode($value) : $value;
}
