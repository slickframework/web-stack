<?php

/**
 * This file is part of web-stack
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Slick\WebStack\Domain\Security\Csrf;

/**
 * TokenGeneratorInterface
 *
 * @package Slick\WebStack\Domain\Security\Csrf
 */
interface TokenGeneratorInterface
{

    /**
     * Generate a token value
     *
     * @return string The generated token value.
     */
    public function generateToken(): string;
}
