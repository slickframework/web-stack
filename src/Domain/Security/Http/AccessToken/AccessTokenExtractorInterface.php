<?php

/**
 * This file is part of web-stack
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Slick\WebStack\Domain\Security\Http\AccessToken;

use Psr\Http\Message\ServerRequestInterface;

/**
 * AccessTokenExtractorInterface
 *
 * @package Slick\WebStack\Domain\Security\Http\AccessToken
 */
interface AccessTokenExtractorInterface
{
    /**
     * Extracts the access token from the given ServerRequestInterface object.
     *
     * @param ServerRequestInterface $request The request object from which to extract the access token
     *
     * @return string|null The access token or null if it cannot be extracted
     */
    public function extractAccessToken(ServerRequestInterface $request): ?string;
}
