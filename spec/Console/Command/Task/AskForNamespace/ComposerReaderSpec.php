<?php

namespace spec\Slick\WebStack\Console\Command\Task\AskForNamespace;

use Slick\WebStack\Console\Command\Task\AskForNamespace\ComposerReader;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Slick\WebStack\Console\Command\Task\AskForNamespace\NameSpaceCollection;
use Slick\WebStack\Console\Command\Task\AskForNamespace\NameSpaceEntry;

class ComposerReaderSpec extends ObjectBehavior
{
    function let()
    {
        $composerFile = getcwd(). '/composer.json';
        $this->beConstructedWith($composerFile);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ComposerReader::class);
    }

    function it_reads_the_composer_file_to_retrieve_a_namespace_collection()
    {
        $collection = $this->nameSpaces();
        $collection->shouldBeAnInstanceOf(NameSpaceCollection::class);
        $collection->offsetGet(0)
            ->shouldBeAnInstanceOf(NameSpaceEntry::class);
    }
}
