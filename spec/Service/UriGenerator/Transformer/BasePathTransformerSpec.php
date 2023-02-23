<?php

/**
 * This file is part of slick/web_stack package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Slick\WebStack\Service\UriGenerator\Transformer;

use PhpSpec\Exception\Example\FailureException;
use PhpSpec\Wrapper\Collaborator;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;
use Slick\WebStack\Service\UriGenerator\LocationTransformerInterface;
use Slick\WebStack\Service\UriGenerator\Transformer\BasePathTransformer;
use PhpSpec\ObjectBehavior;

/**
 * BasePathTransformerSpec specs
 *
 * @package spec\Slick\WebStack\Service\UriGenerator\Transformer
 */
class BasePathTransformerSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(BasePathTransformer::class);
    }

    function it_implements_location_transformer_interface()
    {
        $this->shouldImplement(LocationTransformerInterface::class);
    }

    function it_may_have_an_http_request_as_a_context(
        ServerRequestInterface $request
    ) {
        $this->setRequest($request)
            ->shouldReturn($this->getWrappedObject());
    }

    function it_only_generates_an_uri_if_it_has_an_http_request()
    {
        $this->transform('home')->shouldBeNull();
    }

    function it_adds_the_base_path_to_the_location(
        ServerRequestInterface $request
    )
    {
        $this->prepareRequest($request);
        $this->transform('controller/action')
            ->shouldBeAnUriWithPath('/base/controller/action');
    }

    function it_accepts_query_params_in_options(
        ServerRequestInterface $request
    )
    {
        $this->prepareRequest($request);
        $this->transform('controller/action', ['query' => ['foo' => 'bar']])
            ->shouldBeAnUriWithPath('/base/controller/action?foo=bar');
    }

    function it_can_reuse_host_name_from_context_request(
        ServerRequestInterface $request
    )
    {
        $this->prepareRequest($request);
        $this->transform('controller/action', ['reuseHostName' => 1])
            ->shouldBeAnUriWithPath(
                'https://localhost:12541/base/controller/action'
            );
    }

    function it_can_reuse_the_request_query_params(
        ServerRequestInterface $request
    )
    {
        $this->prepareRequest($request);
        $this->transform('controller/action', [
            'reuseParams' => 1,
            'query' => ['foo' => 'bar']
        ])
            ->shouldBeAnUriWithPath(
                '/base/controller/action?foo=bar&baz=bar'
            );
    }

    /**
     * Prepares the request collaborator
     *
     * @param ServerRequestInterface|Collaborator $request
     */
    private function prepareRequest(ServerRequestInterface $request)
    {
        $serverData = [
            'SCRIPT_NAME' => '/base/test.php',
            'HTTPS' => 'not-empty',
            'SERVER_PORT' => '12541',
            'SERVER_NAME' => 'localhost',
        ];
        $request->getServerParams()
            ->shouldBeCalled()
            ->willReturn($serverData);
        $request->getQueryParams()->willReturn(
            ['foo' => 'bar', 'baz' => 'bar']
        );
        $this->setRequest($request);
    }

    public function getMatchers(): array
    {
        return [
            'beAnUriWithPath' => function ($uri, $path)
            {
                if (!$uri instanceof UriInterface) {
                    $class = UriInterface::class;
                    $type = gettype($uri);
                    throw new FailureException(
                        "Expected {$class} instance, but got '{$type}'"
                    );
                }
                if ($uri->__toString() !== $path) {
                    throw new FailureException(
                        "Expected URI with path '{$path}', but got '{$uri}'"
                    );
                }
                return true;
            }
        ];
    }
}