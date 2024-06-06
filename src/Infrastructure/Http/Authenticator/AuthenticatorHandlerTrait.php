<?php

/**
 * This file is part of php-scaffold
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Slick\WebStack\Infrastructure\Http\Authenticator;

use Slick\WebStack\Domain\Security\Http\Authenticator\AuthenticatorHandlerInterface;
use Slick\WebStack\Domain\Security\Http\AuthenticatorInterface;

/**
 * AuthenticatorHandlerTrait
 *
 * @package Slick\WebStack\Infrastructure\Http\Authenticator
 */
trait AuthenticatorHandlerTrait
{

    protected AuthenticatorHandlerInterface $handler;

    /**
     * @inheritDoc
     */
        public function withHandler(AuthenticatorHandlerInterface $handler): AuthenticatorInterface
    {
        $this->handler = $handler;
        return $this;
    }
}
