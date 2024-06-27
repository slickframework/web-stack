<?php

/**
 * This file is part of web-stack
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Slick\WebStack\Infrastructure;

use Slick\Configuration\Driver\CommonDriverMethods;

/**
 * ArrayConfigurationDriver
 *
 * @package Slick\WebStack\Infrastructure
 */
final class ArrayConfigurationDriver implements ApplicationSettingsInterface
{

    use CommonDriverMethods;

    /**
     * Creates a ArrayConfigurationDriver
     *
     * @param array<string, mixed> $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }
}
