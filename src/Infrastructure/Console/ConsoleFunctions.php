<?php

/**
 * This file is part of web-stack
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Slick\WebStack\Infrastructure\Console;

function constantExists(string $name): bool
{
    return defined($name);
}

function constantValue(string $name, mixed $default = null): mixed
{
    if (constantExists($name)) {
        return constant($name);
    }

    return $default;
}
