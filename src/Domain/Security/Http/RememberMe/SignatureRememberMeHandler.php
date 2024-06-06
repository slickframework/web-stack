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
use Slick\WebStack\Domain\Security\Exception\ExpiredSignatureException;
use Slick\WebStack\Domain\Security\Exception\InvalidSignatureException;
use Slick\WebStack\Domain\Security\Signature\SignatureHasher;
use Slick\WebStack\Domain\Security\User\UserProviderInterface;
use Slick\WebStack\Domain\Security\UserInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;

/**
 * SignatureRememberMeHandler
 *
 * @package Slick\WebStack\Domain\Security\Http\RememberMe
 */
final class SignatureRememberMeHandler extends AbstractRememberMeHandler implements RememberMeHandlerInterface
{

    /**
     * Creates a SignatureRememberMeHandler
     *
     * @param SignatureHasher $hasher
     * @param UserProviderInterface $userProvider
     * @param ServerRequestInterface $request
     * @param array $options
     * @param LoggerInterface|null $logger
     *
     * @phpstan-template U of UserInterface
     * @phpstan-param array<string, mixed> $options
     * @phpstan-param UserProviderInterface<U> $userProvider
     */
    public function __construct(
        private readonly SignatureHasher $hasher,
        UserProviderInterface $userProvider,
        ServerRequestInterface $request,
        array $options = [],
        ?LoggerInterface $logger = null
    ) {
        parent::__construct($userProvider, $request, $options, $logger);
    }

    /**
     * @inheritDoc
     */
    
    protected function processRememberMe(RememberMeDetails $rememberMeDetails, UserInterface $user): void
    {
        $this->hasher->verifySignatureHash($user, $rememberMeDetails->expires(), $rememberMeDetails->value());
        $this->createRememberMeCookie($user);
    }

    /**
     * @inheritDoc
     */
    
    public function createRememberMeCookie(UserInterface $user): void
    {
        $expires = time() + $this->options['lifetime'];
        $value = $this->hasher->computeSignatureHash($user, $expires);

        $details = new RememberMeDetails($user::class, $user->userIdentifier(), $expires, $value);
        $this->createCookie($details);
    }

    /**
     * Consume the RememberMe cookie and validate its hash.
     *
     * @param RememberMeDetails $rememberMeDetails The RememberMe details.
     * @return UserInterface The user associated with the RememberMe cookie.
     * @throws AuthenticationException If the cookie's hash is invalid or has expired.
     */
    public function consumeRememberMeCookie(RememberMeDetails $rememberMeDetails): UserInterface
    {
        try {
            $this->hasher->acceptSignatureHash($rememberMeDetails->userIdentifier(), $rememberMeDetails->expires(), $rememberMeDetails->value());
        } catch (InvalidSignatureException $e) {
            throw new AuthenticationException('The cookie\'s hash is invalid.', 0, $e);
        } catch (ExpiredSignatureException $e) {
            throw new AuthenticationException('The cookie has expired.', 0, $e);
        }

        return parent::consumeRememberMeCookie($rememberMeDetails);
    }
}
