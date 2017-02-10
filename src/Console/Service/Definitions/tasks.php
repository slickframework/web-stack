<?php

namespace Slick\WebStack\Console\Service\Definitions;

use Slick\Di\Definition\ObjectDefinition;
use Slick\WebStack\Console\Command\Task\AskForWebRoot;
use Symfony\Component\Console\Command\Command;

$tasks = [];

$tasks[AskForWebRoot::class] = ObjectDefinition
    ::create(AskForWebRoot::class)
    ->with('@'.Command::class)
;

return $tasks;