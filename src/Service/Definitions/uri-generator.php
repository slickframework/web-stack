<?php

/**
 * This file is part of slick/web_stack package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\WebStack\Services\Definitions;

use Slick\Di\Definition\ObjectDefinition;
use Slick\WebStack\Service\UriGenerator;
use Slick\WebStack\Service\UriGenerator\Transformer\BasePathTransformer;
use Slick\WebStack\Service\UriGenerator\Transformer\RouterPathTransformer;
use Slick\WebStack\Service\UriGenerator\Transformer\FullUrlTransformer;
use Slick\WebStack\Service\UriGeneratorInterface;

$services = [];

$services[UriGeneratorInterface::class] = '@uri.generator';
$services['uri.generator'] = ObjectDefinition::create(UriGenerator::class)
    ->call('addTransformer')->with('@full-url.location.trans')
    ->call('addTransformer')->with('@router.location.trans')
    ->call('addTransformer')->with('@base-path.location.trans');

// *************************
//   LOCATION TRANSFORMERS
// *************************
$services['router.location.trans'] = ObjectDefinition::
    create(RouterPathTransformer::class)
    ->with('@router.container');

$services['base-path.location.trans'] = ObjectDefinition::
    create(BasePathTransformer::class);
$services['full-url.location.trans'] = ObjectDefinition
    ::create(FullUrlTransformer::class);

return $services;