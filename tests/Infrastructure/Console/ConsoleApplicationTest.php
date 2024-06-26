<?php
/**
 * This file is part of web-stack
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Test\Slick\WebStack\Infrastructure\Console;

use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Slick\WebStack\Infrastructure\Console\ConsoleApplication;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\CommandLoader\CommandLoaderInterface;

class ConsoleApplicationTest extends TestCase
{
    use ProphecyTrait;

    public function testInitializable()
    {
        $application = new ConsoleApplication(dirname(__DIR__, 3).'/features/app');
        $this->assertInstanceOf(Application::class, $application->commandLine());
    }

    public function testRun()
    {
        $command = $this->prophesize(Application::class);
        $command->run()->shouldBeCalled();
        $command->setCatchExceptions(true)->shouldBeCalled();
        $command->setCommandLoader(Argument::type(CommandLoaderInterface::class))->shouldBeCalled();
        $command->add(Argument::type(Command::class))->shouldBeCalled();
        $application = new ConsoleApplication(dirname(__DIR__, 3).'/features/app');
        $application->useCommandLine($command->reveal());
        $application->run();
    }
}
