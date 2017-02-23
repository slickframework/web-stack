<?php

namespace Slick\WebStack\Console\Service\Definitions;

use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemInterface;
use Slick\Di\Definition\ObjectDefinition;
use Slick\Template\Template;
use Slick\Template\TemplateEngineInterface;
use Slick\WebStack\Console\Command\Task\AskForNamespace;
use Slick\WebStack\Console\Command\Task\AskForWebRoot;
use Symfony\Component\Console\Command\Command;

$tasks = [];

$tasks[AskForWebRoot::class] = ObjectDefinition
    ::create(AskForWebRoot::class)
    ->with('@'.Command::class)
;
$tasks[AskForNamespace::class] = ObjectDefinition
    ::create(AskForNamespace::class)
    ->with('@'.Command::class, '@composer.reader');

$tasks['composer.reader'] = ObjectDefinition
    ::create(AskForNamespace\ComposerReader::class)
    ->with(getcwd().'/composer.json');

$tasks[AskForNamespace\ComposerReader::class] = '@composer.reader';

$tasks[FilesystemInterface::class] = '@local.filesystem';
$tasks['local.filesystem'] = function () {
    $adapter = new Local(getcwd());
    return new Filesystem($adapter);
};

$tasks[TemplateEngineInterface::class] = '@template.engine';
$tasks['template.engine'] = function () {
    $dir = dirname(dirname(dirname(dirname(__DIR__)))).'/templates';
    Template::appendPath($dir);
    return (new Template())->initialize();
};

return $tasks;