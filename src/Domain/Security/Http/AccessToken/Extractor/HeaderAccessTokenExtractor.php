<?php

/**
 * This file is part of web-stack
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Slick\WebStack\Domain\Security\Http\AccessToken\Extractor;

use Psr\Http\Message\ServerRequestInterface;
use Slick\WebStack\Domain\Security\Http\AccessToken\AccessTokenExtractorInterface;

/**
 * HeaderAccessTokenExtractor
 *
 * @package Slick\WebStack\Domain\Security\Http\AccessToken\Extractor
 */
final class HeaderAccessTokenExtractor implements AccessTokenExtractorInterface
{

    private string $regex;

    public function __construct(
        private readonly string $headerParameter = 'Authorization',
        private readonly string $tokenType = 'Bearer',
    ) {
        $this->regex = sprintf(
            '/^%s([a-zA-Z0-9\-_\+~\/\.]+=*)$/',
            '' === $this->tokenType ? '' : preg_quote($this->tokenType).'\s+'
        );
    }

    /**
     * @inheritDoc
     */
    public function extractAccessToken(ServerRequestInterface $request): ?string
    {
        if (!$request->hasHeader($this->headerParameter) ||
            !is_string($header = $request->getHeaderLine($this->headerParameter))
        ) {
            return null;
        }

        if (preg_match($this->regex, $header, $matches)) {
            return $matches[1];
        }

        return null;
    }
}
