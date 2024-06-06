<?php
/**
 * This file is part of php-scaffold
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Test\Slick\WebStack\Infrastructure\Http\Authenticator\FormLoginAuthenticator;

use Slick\WebStack\Infrastructure\Http\Authenticator\FormLoginAuthenticator\FormLoginProperties;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class FormLoginPropertiesTest extends TestCase
{

    /**
     * @var array<string, mixed> $props Array containing configuration properties.
     */
    private array $props = [
        "paths" => [
            'login' => '/login/path',
            'check' => '/login-check-path',
            'failure' => '/login-fail'
        ],
        "parameters" => [
            "username" => "_username_",
            "password" => "_password_",
            "rememberMe" => "_rememberMe_",
            "csrf" => "_csrf_"
        ],
        "enableCsrf" => false,
        "formOnly" => true,
        "useReferer" => true,
        "rememberMe" => true,
    ];

    #[Test]
    public function initializable(): void
    {
        $properties = new FormLoginProperties($this->props);
        $this->assertInstanceOf(FormLoginProperties::class, $properties);
    }

    #[Test]
    public function paths(): void
    {
        $properties = new FormLoginProperties($this->props);
        $this->assertEquals('/login/path', $properties->path('login'));
        $this->assertEquals('/login-check-path', $properties->path('check'));
        $this->assertEquals('/login-fail', $properties->path('failure'));
    }

    #[Test]
    public function parameters(): void
    {
        $properties = new FormLoginProperties($this->props);
        $this->assertEquals('_username_', $properties->parameter('username'));
    }

    #[Test]
    public function enableCsrf(): void
    {
        $properties = new FormLoginProperties($this->props);
        $this->assertFalse($properties->enableCsrf());
    }

    #[Test]
    public function formOnly(): void
    {
        $properties = new FormLoginProperties($this->props);
        $this->assertTrue($properties->formOnly());
    }

    #[Test]
    public function useReferer(): void
    {
        $properties = new FormLoginProperties($this->props);
        $this->assertTrue($properties->useReferer());
    }

    #[Test]
    public function rememberMe(): void
    {
        $properties = new FormLoginProperties($this->props);
        $this->assertTrue($properties->rememberMe());
    }
}
