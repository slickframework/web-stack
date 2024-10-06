<?php

/**
 * This file is part of web-stack
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Slick\WebStack\Domain\Security\Common;

use Slick\WebStack\Domain\Security\Exception\MissingAttributeException;
use Traversable;

/**
 * AttributesBagMethods
 *
 * @package Slick\WebStack\Domain\Security\Common
 */
trait AttributesBagMethods
{

    /** @var array<string, mixed>  */
    protected array $attributes = [];

    /**
     * @inheritDoc
     */
    public function attributes(): iterable
    {
        return $this->attributes;
    }

    /**
     * @inheritDoc
     */
    public function withAttributes(iterable $attributes): static
    {
        if ($attributes instanceof Traversable) {
            foreach ($attributes as $name => $value) {
                $this->withAttribute($name, $value);
            }
            return $this;
        }

        $this->attributes = (array) $attributes;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function hasAttribute(string $name): bool
    {
        return array_key_exists($name, $this->attributes);
    }

    /**
     * @inheritDoc
     */
    public function attribute(string $name): mixed
    {
        if (!$this->hasAttribute($name)) {
            throw new MissingAttributeException("There are no attribute '$name' in class ".__CLASS__);
        }

        return $this->attributes[$name];
    }

    /**
     * @inheritDoc
     */
    public function withAttribute(string $name, mixed $value): self
    {
        $this->attributes[$name] = $value;
        return $this;
    }
}
