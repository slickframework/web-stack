<?php

/**
 * This file is part of web-stack
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Features\App\UserInterface;

use Psr\Http\Message\ResponseInterface;
use Slick\Http\Message\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * CheckController
 *
 * @package Features\App\UserInterface
 */
final class CheckController
{

    /**
     * Handle the request and return a JSON response.
     *
     * @return ResponseInterface The response.
     */
    #[Route(path: "/check-status", name: 'checkStatus', methods: ['GET'])]
    public function handle(): ResponseInterface
    {
        $body = json_encode(['status' => 'Ok']);
        return new Response(200, $body !== false ? $body : '', ['Content-Type' => 'application/json']);
    }
}
