<?php

/**
 * This file is part of web-stack
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Slick\WebStack;

/**
 * Merges two arrays recursively, overriding values from the default array with values from the custom array.
 *
 * @param array<string, mixed> $default The default array.
 * @param array<string, mixed> $custom The custom array.
 *
 * @return array<string, mixed> The merged array.
 */
function mergeArrays(array $default, array $custom): array
{
    $base = [];
    foreach ($default as $name => $value) {
        $isPresent = array_key_exists($name, $custom);
        if (is_array($value) && $isPresent) {
            $base[$name] = mergeArrays($value, $custom[$name]);
            continue;
        }

        $base[$name] = $isPresent ? $custom[$name] : $value;
    }
    return $base;
}

/**
 * Check if a constant exists.
 *
 * @param string $name The name of the constant.
 * @return bool Returns true if the constant exists, false otherwise.
 */
function constantExists(string $name): bool
{
    return defined($name);
}

/**
 * Get the value of a constant.
 *
 * @param string $name The name of the constant.
 * @param mixed $default The default value to return if the constant does not exist.
 * @return mixed Returns the value of the constant if it exists, or the default value if it does not.
 */
function constantValue(string $name, mixed $default = null): mixed
{
    if (constantExists($name)) {
        return constant($name);
    }

    return $default;
}
