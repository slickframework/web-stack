<?php

/**
 * This file is part of web-stack
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Slick\WebStack\Domain\Security\Http\SecurityProfile;

use Psr\Http\Message\ServerRequestInterface;

/**
 * SecurityProfileTrait
 *
 * @package Slick\WebStack\Domain\Security\Http\SecurityProfile
 */
trait SecurityProfileTrait
{

    protected ?string $matchExp = null;

    /**
     * Checks if the given request matches the defined match expression.
     *
     * @param ServerRequestInterface $request The server request to match against.
     * @return bool Returns true if the request matches the match expression, false otherwise.
     */
    public function match(ServerRequestInterface $request): bool
    {
        $path = $request->getUri()->getPath();
        return $this->matchExp ? (bool) preg_match($this->matchExp, $path) : false;
    }
}
