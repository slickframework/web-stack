<?php

namespace Slick\WebStack\Console\Service\Definitions;

use Slick\Di\Definition\ObjectDefinition;
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
return $tasks;