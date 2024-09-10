<?php

/**
 * This file is part of web-stack
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Slick\WebStack\Domain\Security\Attribute;

use Attribute;
use JsonException;
use Psr\Http\Message\ResponseInterface;
use Slick\Http\Message\Response;
use Slick\WebStack\Domain\Security\Http\HttpStatusCode;

/**
 * IsGranted
 *
 * @package Slick\WebStack\Domain\Security\Attribute
 */
#[Attribute(Attribute::TARGET_METHOD|Attribute::TARGET_CLASS|Attribute::IS_REPEATABLE)]
final readonly class IsGranted
{
    private HttpStatusCode $httpStatusCode;

    /**
     * IsGranted attribute
     *
     * @param string|array<string> $attribute The attribute to verify.
     * @param string $message The error message to display. Default value is "Access denied."
     * @param int $statusCode The HTTP status code to return. Default value is 403.
     * @param array<string, string> $headers
     * @param string|null $location
     * @param bool|null $asJson
     */
    public function __construct(
        private string|array $attribute,
        private string $message = "Access denied.",
        private int $statusCode = 403,
        private array $headers = [],
        private ?string $location = null,
        private ?bool $asJson = false,
    ) {
        $this->httpStatusCode = new HttpStatusCode($this->statusCode);
    }

    /**
     * @return string|array<string>
     */
    public function attribute(): array|string
    {
        return $this->attribute;
    }

    /**
     * Returns a ResponseInterface object for the current request.
     *
     * @return ResponseInterface The response object.
     * @throws JsonException
     */
    public function response(): ResponseInterface
    {
        if ($this->location !== null) {
            return $this->redirectResponse();
        }

        return new Response(status: $this->statusCode, body: $this->message(), headers: $this->headers);
    }

    private function redirectResponse(): ResponseInterface
    {
        $headers = array_merge($this->headers, ['Location' => $this->location]);
        return new Response(status: 302, headers: $headers);
    }

    /**
     * Return the message as a JSON string.
     *
     * @return string The message as a JSON string.
     * @throws JsonException If an error occurs while encoding the message to JSON.
     */
    public function messageAsJson(): string
    {
        $message = [
            "jsonapi" => [
                "version" => "1.1",
            ],
            "errors" => [
                "code" => $this->statusCode,
                "title" => $this->httpStatusCode->description(),
                "detail" => $this->message,
            ]
        ];
        return json_encode($message, JSON_THROW_ON_ERROR);
    }

    /**
     * Get the error message.
     *
     * If the 'asJson' flag is set to true, the error message will be returned as a JSON string.
     * Otherwise, the error message will be returned as a regular string.
     *
     * @return string The error message.
     * @throws JsonException
     */
    public function message(): string
    {
        if ($this->asJson) {
            return $this->messageAsJson();
        }
        return $this->message;
    }
}
