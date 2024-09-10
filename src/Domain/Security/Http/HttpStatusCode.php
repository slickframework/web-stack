<?php

/**
 * This file is part of web-stack
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Slick\WebStack\Domain\Security\Http;

/**
 * HttpStatusCode
 *
 * @package Slick\WebStack\Domain\Security\Http
 */
final class HttpStatusCode
{

    /**
     * @var array<array{code: int, description: string}>
     */
    private static array $codes = [];

    private int $code;
    private string $description;

    public function __construct(int $statusCode)
    {
        $statusCodesJson = file_get_contents(__DIR__ . "/http_codes.json");
        $code = null;

        if ($statusCodesJson) {
            self::$codes = json_decode($statusCodesJson, true);
            $code = $this->search($statusCode);
        }


        if (!$code) {
            $this->code = $statusCode;
            $this->description = "Unknown HTTP status code: $statusCode";
            return;
        }
        $this->code = $code['code'];
        $this->description = $code['description'];
    }

    /**
     * @return int
     */
    public function code(): int
    {
        return $this->code;
    }

    /**
     * @return string
     */
    public function description(): string
    {
        return $this->description;
    }

    /**
     * @param int $statusCode
     * @return array{code: int, description: string}|null
     */
    private function search(int $statusCode): ?array
    {
        foreach (self::$codes as $code) {
            if ($code['code'] === $statusCode) {
                return $code;
            }
        }
        return null;
    }
}
