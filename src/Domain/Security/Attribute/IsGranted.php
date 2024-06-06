<?php

/**
 * This file is part of web-stack
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Slick\WebStack\Domain\Security\Attribute;

use Attribute;

/**
 * IsGranted
 *
 * @package Slick\WebStack\Domain\Security\Attribute
 */
#[Attribute(Attribute::TARGET_METHOD|Attribute::TARGET_CLASS|Attribute::IS_REPEATABLE)]
final readonly class IsGranted
{

    /**
     * IsGranted attribute
     *
     * @param string|array<string> $attribute The attribute to verify.
     * @param string $message The error message to display. Default value is "Access denied."
     * @param int $statusCode The HTTP status code to return. Default value is 403.
     */
    public function __construct(
        public string|array $attribute,
        public string $message = "Access denied.",
        public int $statusCode = 403
    ) {
    }
}
