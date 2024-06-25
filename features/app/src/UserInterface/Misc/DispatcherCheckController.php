<?php

/**
 * This file is part of web-stack
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Features\App\UserInterface\Misc;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slick\Http\Message\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * DispatcherCheckController
 *
 * @package Features\App\UserInterface\Misc
 */
final class DispatcherCheckController
{

    #[Route(path: '/misc/check-status/{param}', name: 'misc.checkStatus', methods: ['GET'])]
    public function simple(ServerRequestInterface $request, string $param = null): ResponseInterface
    {
        return new Response(200, '
                <h1>Web stack application demo</h1>
                <dl>
                    <dt>param:</dt>
                    <dd>' . $param . '</dd>
                    <dt>request:</dt>
                    <dd>' . $request->getMethod() ." ". $request->getUri()->getPath() . '</dd>
                </dl>
            ', ['content-type' => 'text/html']);
    }

}
