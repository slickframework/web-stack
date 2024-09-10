<?php

/**
 * This file is part of web-stack
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Slick\WebStack\Domain\Security\Http\SecurityProfile;

use Slick\WebStack\Domain\Security\Http\AuthenticationEntryPointInterface;

/**
 * EntryPointAwareInterface
 *
 * @package Slick\WebStack\Domain\Security\Http\SecurityProfile
 */
interface EntryPointAwareInterface
{

    /**
     * AuthenticatorsListFactory entryPoint
     *
     * @return AuthenticationEntryPointInterface|null
     */
    public function entryPoint(): ?AuthenticationEntryPointInterface;

    /**
     * Sets the entry point for the authenticators list factory.
     *
     * @param AuthenticationEntryPointInterface $entryPoint The entry point implementation.
     */
    public function withEntryPoint(AuthenticationEntryPointInterface $entryPoint): void;
}
