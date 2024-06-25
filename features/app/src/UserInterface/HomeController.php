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
use Slick\Di\ContainerInterface;
use Symfony\Component\Routing\Attribute\Route;

/**
 * HomeController
 *
 * @package Features\App\UserInterface
 */
final class HomeController
{


    #[Route(path: '/', name: 'home')]
    public function handler(ContainerInterface $container): ResponseInterface
    {
        $default = $container->get('default.middleware');
        return $default();
    }
}
