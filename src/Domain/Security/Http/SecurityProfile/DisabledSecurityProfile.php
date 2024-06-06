<?php

/**
 * This file is part of web-stack
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Slick\WebStack\Domain\Security\Http\SecurityProfile;

use Slick\WebStack\Domain\Security\Http\SecurityProfileInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * DisabledSecurityProfile
 *
 * @package Slick\WebStack\Domain\Security\Http\SecurityProfile
 */
final class DisabledSecurityProfile implements SecurityProfileInterface
{

    use SecurityProfileTrait;

    /**
     * Creates a Disabled Security Profile
     *
     * @param string $matchExp The match expression.
     */
    public function __construct(string $matchExp)
    {
        $this->matchExp = $matchExp;
    }

    /**
     * @inheritDoc
     */
    
    public function process(ServerRequestInterface $request): ?ResponseInterface
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    
    public function authenticationErrors(): array
    {
        return [];
    }
}
