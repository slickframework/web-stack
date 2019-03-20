<?php

/**
 * This file is part of WebStack package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Features\App\Articles\Controller;

use Slick\WebStack\Controller\ControllerMethods;
use Slick\WebStack\ControllerInterface;

/**
 * ReadArticle
 *
 * @package Features\App\Articles\Controller
 */
final class ReadArticle implements ControllerInterface
{

    use ControllerMethods;

    public function handle(): void
    {
        $this->set('articleId', $this->context->routeParam('articleId'));
    }
}
