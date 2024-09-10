<?php
/**
 * This file is part of php-scaffold
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Test\Slick\WebStack\Domain\Security\Http\RememberMe;

use Slick\WebStack\Domain\Security\Exception\AuthenticationException;
use Slick\WebStack\Domain\Security\Http\RememberMe\RememberMeDetails;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Stringable;
use Test\Slick\WebStack\Domain\Security\Signature\DummyUser;

class RememberMeDetailsTest extends TestCase
{

    private $expires;

    #[Test]
    public function initializable(): void
    {
        $rememberMeDetails = $this->createRememberMeDetails();
        $this->assertInstanceOf(RememberMeDetails::class, $rememberMeDetails);
    }

    #[Test]
    public function itHasAUserFqcn()
    {
        $rememberMeDetails = $this->createRememberMeDetails();
        $this->assertEquals(DummyUser::class, $rememberMeDetails->userFqcn());
    }

    #[Test]
    public function itHasAUserIdentifier()
    {
        $rememberMeDetails = $this->createRememberMeDetails();
        $this->assertEquals('userIdentifier', $rememberMeDetails->userIdentifier());
    }

    #[Test]
    public function itHasAnExpiresTime()
    {
        $rememberMeDetails = $this->createRememberMeDetails();
        $this->assertEquals($this->expires, $rememberMeDetails->expires());
    }

    #[Test]
    public function itHasValue()
    {
        $rememberMeDetails = $this->createRememberMeDetails();
        $this->assertEquals('Some value', $rememberMeDetails->value());
    }

    #[Test]
    public function __canChangeItsValue(): void
    {
        $rememberMeDetails = $this->createRememberMeDetails();
        $newOne = $rememberMeDetails->withValue('Other value');
        $this->assertNotSame($newOne, $rememberMeDetails);
        $this->assertEquals('Some value', $rememberMeDetails->value());
        $this->assertEquals('Other value', $newOne->value());
    }

    #[Test]
    public function canBeCreatedFromRawCookie(): void
    {
        $fqcn = strtr(DummyUser::class, '\\', '.');
        $userIdentifier = strtr(base64_encode('userIdentifier'), '+/=', '-_~');
        $expires = time() + 60 * 60 * 24;
        $value = 'Some value';
        $rawCookie = implode(RememberMeDetails::COOKIE_DELIMITER , [$fqcn, $userIdentifier, $expires, $value]);
        $details = RememberMeDetails::fromRawCookie(base64_encode($rawCookie));
        $this->assertInstanceOf(RememberMeDetails::class, $details);
    }

    #[Test]
    public function badCookie(): void
    {
        $fqcn = strtr(DummyUser::class, '\\', '.');
        $userIdentifier = strtr(base64_encode('userIdentifier'), '+/=', '-_~');
        $expires = time() + 60 * 60 * 24;
        $rawCookie = implode(RememberMeDetails::COOKIE_DELIMITER , [$fqcn, $userIdentifier, $expires]);
        $this->expectException(AuthenticationException::class);
        $details = RememberMeDetails::fromRawCookie(base64_encode($rawCookie));
        $this->assertNull($details);
    }

    #[Test]
    public function badUserIdentifier(): void
    {
        $fqcn = strtr(DummyUser::class, '\\', '.');
        $userIdentifier = '?'.substr(strtr(base64_encode('userIdentifier'), '+/=', '-_~'), 1);
        $expires = time() + 60 * 60 * 24;
        $value = 'Some value';
        $rawCookie = implode(RememberMeDetails::COOKIE_DELIMITER , [$fqcn, $userIdentifier, $expires, $value]);
        $this->expectException(AuthenticationException::class);
        $details = RememberMeDetails::fromRawCookie(base64_encode($rawCookie));
        $this->assertNull($details);
    }

    #[Test]
    public function canBeConvertedToString(): void
    {
        $rememberMeDetails = $this->createRememberMeDetails();
        $this->assertInstanceOf(Stringable::class, $rememberMeDetails);
        $this->assertEquals(
                implode(RememberMeDetails::COOKIE_DELIMITER, [
                    strtr(DummyUser::class, '\\', '.'),
                    strtr(base64_encode('userIdentifier'), '+/=', '-_~'),
                    $this->expires,
                    'Some value'
                ]),
                (string) $rememberMeDetails
            );
    }

    /**
     * @return RememberMeDetails
     */
    public function createRememberMeDetails(): RememberMeDetails
    {
        $this->expires = time() + 60 * 60 * 24;
        $rememberMeDetails = new RememberMeDetails(
            DummyUser::class,
            'userIdentifier',
            $this->expires,
            'Some value'
        );
        return $rememberMeDetails;
    }
}
