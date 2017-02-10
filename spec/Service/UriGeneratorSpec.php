<?php

namespace spec\Slick\WebStack\Service;

use PhpSpec\ObjectBehavior;
use Psr\Http\Message\ServerRequestInterface;
use Slick\Http\Uri;
use Slick\WebStack\Service\UriGenerator;
use Slick\WebStack\Service\UriGeneratorInterface;

/**
 * UriGeneratorSpec
 *
 * @package spec\Slick\WebStack\Service
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
class UriGeneratorSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(UriGenerator::class);
    }

    function its_a_uri_generator()
    {
        $this->shouldImplement(UriGeneratorInterface::class);
    }

    function it_has_a_collection_of_location_transformers(
        UriGenerator\LocationTransformerInterface $transformer
    )
    {
        $this->addTransformer($transformer)->shouldBe($this->getWrappedObject());
    }

    function it_has_a_collection_of_uri_decorators(
        UriGenerator\UriDecoratorInterface $decorator
    )
    {
        $this->addDecorator($decorator)->shouldBe($this->getWrappedObject());
    }

    function it_cycles_through_the_transformers_to_generate_the_uri(
        UriGenerator\LocationTransformerInterface $transformerInactive,
        UriGenerator\LocationTransformerInterface $transformerActive,
        UriGenerator\UriDecoratorInterface $decorator
    )
    {
        $location = 'test';
        $options = ['foo' => 'bar'];
        $uri = new Uri();
        $transformerActive->transform($location, $options)
            ->shouldBeCalled()
            ->willReturn($uri);

        $this->addTransformer($transformerInactive)
            ->addTransformer($transformerActive);
        $this->addDecorator($decorator);

        $this->generate($location, $options)->shouldBe($uri);

        $transformerInactive->transform($location, $options)->shouldHaveBeenCalled();
        $decorator->decorate($uri)->shouldHaveBeenCalled();
    }

    function it_may_have_an_http_request_as_a_context(
        ServerRequestInterface $request,
        UriGenerator\LocationTransformerInterface $transformer
    )
    {
        $location = 'test';
        $options = ['foo' => 'bar'];
        $uri = new Uri();
        $transformer->transform($location, $options)
            ->shouldBeCalled()
            ->willReturn($uri);
        $transformer->setRequest($request)->shouldBeCalled();

        $this->setRequest($request)->shouldBe($this->getWrappedObject());
        $this->addTransformer($transformer);
        $this->generate($location, $options);
    }
}
