<?php

/**
 * This file is part of php-scaffold
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Slick\WebStack\Infrastructure\Http\Authenticator\FormLoginAuthenticator;

/**
 * FormLoginProperties
 *
 * @package Slick\WebStack\Infrastructure\Http\Authenticator\FormLoginAuthenticator
 */
final class FormLoginProperties
{
    /** @var array<string, mixed> */
    private readonly array $properties;

    /** @var array<string, mixed>  */
    private static array $defaultProperties = [
        "paths" => [
            'login' => '/login',
            'check' => '/login-check',
            'failure' => '/login',
            'defaultTarget' => '/'
        ],
        "parameters" => [
            "username" => "_username",
            "password" => "_password",
            "rememberMe" => "_rememberMe",
            "csrf" => "_csrf"
        ],
        "enableCsrf" => true,
        "formOnly" => false,
        "useReferer" => false,
        "rememberMe" => false,
    ];

    /**
     * Creates FormLoginProperties object
     *
     * @param array<string, mixed> $properties The properties to be set for the class.
     */
    public function __construct(array $properties)
    {
        $this->properties = array_merge(self::$defaultProperties, $properties);
    }

    /**
     * Returns the paths based on the given type.
     *
     * @param string $type The type of paths to retrieve.
     * @return string The paths associated with the given type.
     */
    public function path(string $type): string
    {
        return $this->properties['paths'][$type] ?? "";
    }

    /**
     * Retrieves the name of the parameter used to store a given login info
     *
     * @param string $name The name of the parameter to retrieve.
     *
     * @return string|null The value of the parameter.
     */
    public function parameter(string $name): ?string
    {
        return $this->properties['parameters'][$name] ?? null;
    }

    /**
     * Enable CSRF
     *
     * Return boolean indicating if CSRF is enabled or not
     *
     * @return bool Indicates if CSRF is enabled
     */
    public function enableCsrf(): bool
    {
        return (bool) $this->properties['enableCsrf'];
    }

    /**
     * Require that the login data is sent using a form
     *
     * @return bool Whether data is sent using a form.
     */
    public function formOnly(): bool
    {
        return (bool) $this->properties['formOnly'];
    }

    /**
     * Should redirect to the value stored in the HTTP_REFERER header
     *
     * @return bool True if the HTTP_REFERER header should be used, false otherwise.
     */
    public function useReferer(): bool
    {
        return (bool)$this->properties['useReferer'];
    }

    /**
     * Checks if remember me functionality is enabled
     *
     * @return bool Whether remember me functionality is enabled or not.
     */
    public function rememberMe(): bool
    {
        return (bool)$this->properties['rememberMe'];
    }

    /**
     * Retrieve the paths array
     *
     * @return array<string> The paths array
     */
    public function paths(): array
    {
        return $this->properties['paths'];
    }
}
