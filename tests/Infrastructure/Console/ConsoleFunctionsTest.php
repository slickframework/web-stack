<?php

/**
 * This file is part of web-stack
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Test\Slick\WebStack\Infrastructure\Console;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use function Slick\WebStack\Infrastructure\Console\constantValue;

/**
 * ConsoleFunctionsTest
 *
 * @package Test\Slick\WebStack\Infrastructure\Console
 */
final class ConsoleFunctionsTest extends TestCase
{
    #[Test]
    public function checkConstant(): void
    {
        $this->assertEquals(\APP_ROOT, constantValue('APP_ROOT', '/test'));
        $this->assertEquals('test', constantValue('_OTHER_CONST', 'test'));
    }
}
