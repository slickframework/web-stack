<?php

/**
 * This file is part of slick/web_stack package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Slick\WebStack\Service;

use Psr\Http\Message\ServerRequestInterface;
use Slick\Http\Message\Uri;
use Slick\WebStack\Service\UriGenerator;
use PhpSpec\ObjectBehavior;
use Slick\WebStack\Service\UriGeneratorInterface;

/**
 * UriGeneratorSpec specs
 *
 * @package spec\Slick\WebStack\Service
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
        $uri = new Uri('http://example.com');
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
        $uri = new Uri('http://example.com');
        $transformer->transform($location, $options)
            ->shouldBeCalled()
            ->willReturn($uri);
        $transformer->setRequest($request)->shouldBeCalled();
        $this->setRequest($request)->shouldBe($this->getWrappedObject());
        $this->addTransformer($transformer);
        $this->generate($location, $options);
    }
}