<?php
/**
 * This file is part of web-stack
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Test\Slick\WebStack\Infrastructure;

use PHPUnit\Framework\Attributes\Test;
use Slick\WebStack\Infrastructure\ComposerParser;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class ComposerParserTest extends TestCase
{
    #[Test]
    public function initializable(): void
    {
        $composer = dirname(__DIR__, 2) . '/composer.json';
        $this->assertFileExists($composer);
        $parser = new ComposerParser($composer);
        $this->assertInstanceOf(ComposerParser::class, $parser);
    }

    #[Test]
    public function appName(): void
    {
        $composer = dirname(__DIR__, 2) . '/composer.json';
        $parser = new ComposerParser($composer);
        list($name, $version) = $this->readComposer($composer);
        $parserAppName = $this->inflateName($name);
        $this->assertEquals($parserAppName, $parser->appName());
    }

    #[Test]
    public function version(): void
    {
        $composer = dirname(__DIR__, 2) . '/composer.json';
        $parser = new ComposerParser($composer);
        list($name, $version) = $this->readComposer($composer);
        $this->assertEquals($version, $parser->version());
    }

    #[Test]
    public function invalid(): void
    {
        $composer = dirname(__DIR__, 2) . '/test.json';
        $this->expectException(RuntimeException::class);
        $parser = new ComposerParser($composer);
        $this->assertNull($parser);
    }

    #[Test]
    public function description(): void
    {
        $composer = dirname(__DIR__, 2) . '/composer.json';
        $data = json_decode(file_get_contents($composer), true);
        $parser = new ComposerParser($composer);
        $this->assertSame($data["description"], $parser->description());
    }

    #[Test]
    public function psr4Namespaces(): void
    {
        $composer = dirname(__DIR__, 2) . '/composer.json';
        $data = json_decode(file_get_contents($composer), true);
        $parser = new ComposerParser($composer);
        $this->assertEquals(array_keys($data["autoload"]["psr-4"]), $parser->psr4Namespaces());
    }

    private function readComposer(string $composerFile): array
    {
        $data = json_decode(file_get_contents($composerFile), true);
        $name = $data['name'];
        $version = $data['version'];
        return [$name, $version];
    }

    private function inflateName(string $name): string
    {
        $parts = explode('/', str_replace(['-', '_', '.'], ' ', $name));
        $owner = ucwords(trim($parts[0]));
        $app = ucfirst(trim($parts[1]));
        return "$owner's $app";
    }
}
