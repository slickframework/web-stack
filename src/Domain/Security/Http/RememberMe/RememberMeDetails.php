<?php

/**
 * This file is part of web-stack
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Slick\WebStack\Domain\Security\Http\RememberMe;

use Slick\WebStack\Domain\Security\Exception\AuthenticationException;
use Stringable;

/**
 * RememberMeDetails
 *
 * @package Slick\WebStack\Domain\Security\Http\RememberMe
 */
final class RememberMeDetails implements Stringable
{
    public const COOKIE_DELIMITER = ':';

    public function __construct(
        private readonly string $userFqcn,
        private readonly string $userIdentifier,
        private readonly int|string $expires,
        private string $value
    ) {
    }

    public static function fromRawCookie(string $rawCookie): RememberMeDetails
    {
        if (!str_contains($rawCookie, self::COOKIE_DELIMITER)) {
            $rawCookie = base64_decode($rawCookie);
        }

        $cookieParts = explode(self::COOKIE_DELIMITER, $rawCookie, 4);
        if (4 !== count($cookieParts)) {
            throw new AuthenticationException('The cookie contains invalid data.');
        }

        if (false === $cookieParts[1] = base64_decode(strtr($cookieParts[1], '-_~', '+/='), true)) {
            throw new AuthenticationException('The user identifier contains a character from outside the base64 alphabet.');
        }

        $params = [
            'userFqcn' => strtr((string) $cookieParts[0], '.', '\\'),
            'userIdentifier' => $cookieParts[1],
            'expires' => (int) $cookieParts[2],
            'value' => (string) $cookieParts[3],
        ];

        return new RememberMeDetails(...$params);
    }

    /**
     * RememberMeDetails userFqcn
     *
     * @return string
     */
    public function userFqcn(): string
    {
        return $this->userFqcn;
    }

    /**
     * RememberMeDetails userIdentifier
     *
     * @return string
     */
    public function userIdentifier(): string
    {
        return $this->userIdentifier;
    }

    /**
     * RememberMeDetails expires
     *
     * @return int
     */
    public function expires(): int
    {
        return (int) $this->expires;
    }

    /**
     * RememberMeDetails value
     *
     * @return string
     */
    public function value(): string
    {
        return $this->value;
    }

    /**
     * Clones the object and sets the value property with the provided value.
     *
     * @param string $value The value to set on the cloned object.
     *
     * @return RememberMeDetails The cloned object with the updated value.
     */
    public function withValue(string $value): RememberMeDetails
    {
        $details = clone $this;
        $details->value = $value;

        return $details;
    }

    /**
     * @inheritDoc
     */
    public function __toString(): string
    {
        return implode(
            self::COOKIE_DELIMITER,
            [
                strtr($this->userFqcn, '\\', '.'),
                strtr(base64_encode($this->userIdentifier), '+/=', '-_~'),
                $this->expires,
                $this->value
            ]
        );
    }
}
