<?php

namespace spec\Slick\WebStack\Console\Command\Task\AskForNamespace;

use Slick\Common\Utils\CollectionInterface;
use Slick\WebStack\Console\Command\Task\AskForNamespace\NameSpaceCollection;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Slick\WebStack\Console\Command\Task\AskForNamespace\NameSpaceEntry;

class NameSpaceCollectionSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(NameSpaceCollection::class);
    }

    function its_a_collection_of_namespace_entries()
    {
        $this->shouldBeAnInstanceOf(CollectionInterface::class);
    }

    function it_only_accepts_namespace_entries()
    {
        $nameSpace = new NameSpaceEntry('Foo\\Bar', 'src');
        $this->add($nameSpace)->shouldBe($this->getWrappedObject());
    }
}
