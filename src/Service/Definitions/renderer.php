<?php

/**
 * This file is part of slick/web_stack package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\WebStack\Services\Definitions;

use Slick\Di\ContainerInterface;
use Slick\Di\Definition\ObjectDefinition;
use Slick\WebStack\Http\Renderer\TemplateExtension\HtmlExtension;
use Slick\WebStack\Http\Renderer\ViewInflector;
use Slick\WebStack\Http\Renderer\ViewInflectorInterface;
use Slick\WebStack\Http\RendererMiddleware;
use Slick\Template\Template;

$services = [];
Template::appendPath(dirname(dirname(dirname(__DIR__))).'/templates');

// HTML SERVER RENDERER MIDDLEWARE
$services['renderer.middleware'] = ObjectDefinition
    ::create(RendererMiddleware::class)
    ->with('@template.engine', '@view.inflector')
;

// VIEW INFLECTOR
$services[ViewInflectorInterface::class] = '@view.inflector';
$services['view.inflector'] = ObjectDefinition
    ::create(ViewInflector::class)
    ->with('twig')
;

// TEMPLATE DEFINITIONS
$services['template.engine'] = function (ContainerInterface $container) {
    $template = new Template(Template::ENGINE_TWIG);
    $template->addExtension($container->get('html.extension'));
    return $template->initialize();
};
$services['html.extension'] = ObjectDefinition
    ::create(HtmlExtension::class)
    ->with('@uri.generator');

return $services;