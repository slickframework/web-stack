<?php

/**
 * This file is part of web-stack
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Test\Slick\WebStack;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use function Slick\WebStack\constantValue;
use function Slick\WebStack\importSettingsFile;

/**
 * FunctionsTest
 *
 * @package Test\Slick\WebStack
 */
class FunctionsTest extends TestCase
{

    #[Test]
    public function checkConstant(): void
    {
        $this->assertEquals(APP_ROOT, constantValue('APP_ROOT', '/test'));
        $this->assertEquals('test', constantValue('_OTHER_CONST', 'test'));
    }

    #[Test]
    public function importSettings(): void
    {
        $file = '/some/file.php';
        $this->assertEquals([], importSettingsFile($file));
    }
}
