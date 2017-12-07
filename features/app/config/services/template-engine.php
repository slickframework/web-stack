<?php

/**
 * Template engine service
 */

namespace Features\App\Config\Services;

use Slick\Di\Container;
use Slick\Template\Template;
use Slick\Template\TemplateEngineInterface;
use Slick\WebStack\Renderer\Extension\HtmlExtension;

$services = [];

// You can add your template paths here
Template::addPath(APP_ROOT.'/templates');

$services[TemplateEngineInterface::class] = '@template.engine';
$services['template.engine'] = function (Container $container) {
    $template = new Template();
    $template->addExtension(new HtmlExtension($container->get('uri.generator')));
    return $template->initialize();
};

return $services;