<?php

/**
 * This file is part of php-scaffold
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Test\Slick\WebStack\Infrastructure\Http\Dispatcher;

use Psr\Http\Message\ResponseInterface;
use Slick\Http\Message\Response;

/**
 * SimpleController
 *
 * @package Infrastructure\Http\Dispatcher
 */
final class SimpleController
{

    public function action(): ResponseInterface
    {
        return new Response(200, 'Test');
    }

    public function noResponse(): bool
    {
        return true;
    }

    public function withRouteParams(string $id): ResponseInterface
    {
        return new Response(200, $id);
    }

    public function withContainerParam(SomeInterface $tool, string $id, ?string $other = null): ResponseInterface
    {
        return new Response(200, $id);
    }

    public function missing(string $needed): ResponseInterface
    {
        return new Response(200, $needed);
    }
}
