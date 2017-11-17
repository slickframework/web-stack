<?php

/**
 * This file is part of slick/web_stack package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\WebStack\Service\UriGenerator\Transformer;

use Slick\Http\Message\Uri;

/**
 * Custom URI
 *
 * @package Slick\WebStack\Service\UriGenerator\Transformer
 */
class CustomUri extends Uri
{

    /**
     * Creates an empty HTTP URI
     * @param null $url
     */
    public function __construct($url = null)
    {
        if (null == $url) {
            return;
        }

        parent::__construct($url);
    }
}
