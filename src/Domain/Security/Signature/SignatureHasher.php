<?php

/**
 * This file is part of web-stack
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Slick\WebStack\Domain\Security\Signature;

use Slick\WebStack\Domain\Security\Exception\ExpiredSignatureException;
use Slick\WebStack\Domain\Security\Exception\InvalidSignatureException;
use Slick\WebStack\Domain\Security\UserInterface;
use DateTimeInterface;
use ReflectionException;
use ReflectionProperty;
use SensitiveParameter;
use Stringable;

/**
 * SignatureHasher
 *
 * @package Slick\WebStack\Domain\Security\Signature
 */
final readonly class SignatureHasher
{

    /**
     * Creates a SignatureHasher
     *
     * @param string $secret
     * @param array<string> $signatureProperties
     */
    public function __construct(
        #[SensitiveParameter]
        private string $secret,
        private array  $signatureProperties = [],
    ) {
    }

    /**
     * Computes the signature hash for a UserInterface object.
     *
     * @param UserInterface $user The UserInterface object to compute the signature hash for.
     * @param int $expires The expiration time for the signature.
     *
     * @return string The computed signature hash.
     * @throws InvalidSignatureException When the property path returns a non-string value.
     */
    public function computeSignatureHash(UserInterface $user, int $expires): string
    {
        $userIdentifier = $user->userIdentifier();
        $fieldsHash = hash_init('sha256');

        foreach ($this->signatureProperties as $property) {
            $value = $this->retrieveValue($user, $property) ?? '';
            if ($value instanceof DateTimeInterface) {
                $value = $value->format('c');
            }

            if (!is_scalar($value) && !$value instanceof Stringable) {
                throw new InvalidSignatureException(
                    sprintf(
                        'The property path "%s" on the user object "%s" must return a value that can be cast to a ' .
                        'string, but "%s" was returned.',
                        $property,
                        $user::class,
                        get_debug_type($value)
                    )
                );
            }
            hash_update($fieldsHash, ':'.base64_encode((string) $value));
        }

        $fieldsHash = strtr(base64_encode(hash_final($fieldsHash, true)), '+/=', '-_~');
        return $this->generateHash($fieldsHash.':'.$expires.':'.$userIdentifier).$fieldsHash;
    }

    /**
     * Verifies the hash using the provided user identifier and expire time.
     *
     * This method must be called before the user object is loaded from a provider.
     *
     * @param string $identifier The identifier associated with the signature.
     * @param int $expires The expiration timestamp associated with the signature.
     * @param string $hash The signature hash to be validated.
     *
     * @throws InvalidSignatureException When the signature is invalid or expired.
     */
    public function acceptSignatureHash(string $identifier, int $expires, string $hash): void
    {
        $this->checkExpiration($expires);

        $hmac = substr($hash, 0, 44);
        $payload = substr($hash, 44).':'.$expires.':'.$identifier;

        if (!hash_equals($hmac, $this->generateHash($payload))) {
            throw new InvalidSignatureException('Invalid or expired signature.');
        }
    }

    /**
     * Verifies the hash using the provided user and expire time.
     *
     * @param UserInterface $user The UserInterface object to verify the signature hash for.
     * @param int $expires The expiration time for the signature.
     * @param string $hash The hash to compare against the computed signature hash.
     *
     * @throws InvalidSignatureException When the signature is invalid or expired.
     */
    public function verifySignatureHash(UserInterface $user, int $expires, string $hash): void
    {
        $this->checkExpiration($expires);

        if (!hash_equals($hash, $this->computeSignatureHash($user, $expires))) {
            throw new InvalidSignatureException('Invalid or expired signature.');
        }
    }

    private function generateHash(string $tokenValue): string
    {
        return strtr(base64_encode(hash_hmac('sha256', $tokenValue, $this->secret, true)), '+/=', '-_~');
    }

    /**
     * Checks if a signature has expired based on the provided expiration time.
     *
     * @param int $expires The expiration time in seconds.
     * @throws ExpiredSignatureException When the signature has expired.
     */
    public function checkExpiration(int $expires): void
    {
        if ($expires < time()) {
            throw new ExpiredSignatureException('Signature has expired.');
        }
    }

    /**
     * Retrieves the value of a specified property from a UserInterface object.
     *
     * @param UserInterface $user The UserInterface object from which to retrieve the property value.
     * @param string $property The name of the property to retrieve the value for.
     * @return mixed The value of the specified property.
     * @throws InvalidSignatureException When the property does not exist or could not be retrieved.
     */
    private function retrieveValue(UserInterface $user, string $property): mixed
    {
        try {
            $propertyReflection = new ReflectionProperty($user, $property);
        } catch (ReflectionException) {
            throw new InvalidSignatureException('User property does not exist or could not be retrieved.');
        }

        #pragma warning disable S3011
        return $propertyReflection->getValue($user);
    }
}
