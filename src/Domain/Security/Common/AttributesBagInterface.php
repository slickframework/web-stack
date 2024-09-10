<?php

/**
 * This file is part of web-stack
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Slick\WebStack\Domain\Security\Common;

use InvalidArgumentException;

/**
 * AttributesBagInterface
 *
 * @package Slick\WebStack\Domain\Security\Common
 */
interface AttributesBagInterface
{
    /**
     * Retrieves the attributes of an object.
     *
     * @return iterable<string, mixed> The attributes of the object.
     */
    public function attributes(): Iterable;

    /**
     * Sets the provided attributes.
     *
     * @param iterable<string, mixed> $attributes The attributes to set on the object.
     * @return static Instance of the current object.
     */
    public function withAttributes(iterable $attributes): static;

    /**
     * Checks if an attribute with the given name exists
     *
     * @param string $name The name of the attribute
     * @return bool Returns true if the attribute exists, false otherwise
     */
    public function hasAttribute(string $name): bool;

    /**
     * Gets the value of the attribute with the given name
     *
     * @param string $name The name of the attribute
     * @return mixed|null Returns the value of the attribute if it exists, null otherwise
     *
     * @throws InvalidArgumentException When attribute doesn't exist for this token
     */
    public function attribute(string $name): mixed;

    /**
     * Adds or replaces an attribute with the given name and value.
     *
     * @param string $name The name of the attribute.
     * @param mixed $value The value of the attribute.
     * @return self This method returns the current instance of the object.
     */
    public function withAttribute(string $name, mixed $value): self;
}
