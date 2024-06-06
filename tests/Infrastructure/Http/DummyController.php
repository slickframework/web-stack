<?php

/**
 * This file is part of php-scaffold
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Test\Slick\WebStack\Infrastructure\Http;

use Slick\WebStack\Domain\Security\Attribute\IsGranted;

/**
 * DummyController
 *
 * @package Test\Slick\WebStack\Infrastructure\Http
 */
#[IsGranted("ROLE_USER")]
final class DummyController
{

    #[IsGranted("ROLE_ADMIN")]
    public function handle(): void
    {
    }
}
