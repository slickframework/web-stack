<?php

namespace spec\Slick\WebStack\Service\UriGenerator\Transformer;

use Aura\Router\Exception\RouteNotFound;
use Aura\Router\Generator;
use Aura\Router\RouterContainer;
use PhpSpec\Exception\Example\FailureException;
use PhpSpec\Wrapper\Collaborator;
use Prophecy\Argument;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;
use Slick\WebStack\Service\UriGenerator\LocationTransformerInterface;
use Slick\WebStack\Service\UriGenerator\Transformer\RouterPathTransformer;
use PhpSpec\ObjectBehavior;

/**
 * RouterPathTransformerSpec
 *
 * @package spec\Slick\WebStack\Service\UriGenerator\Transformer
 * @author  Filipe Silva <filipe.silva@sata.pt>
 */
class RouterPathTransformerSpec extends ObjectBehavior
{
    function let(RouterContainer $router)
    {
        $this->beConstructedWith($router);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(RouterPathTransformer::class);
    }

    function it_implements_location_transformer_interface()
    {
        $this->shouldImplement(LocationTransformerInterface::class);
    }

    function it_will_return_null_for_unknown_routes(
        RouterContainer $router,
        Generator $generator
    )
    {
        $router->getGenerator()->willReturn($generator);
        $this->beConstructedWith($router);

        $generator->generate('test', [])
            ->shouldBeCalled()
            ->willThrow(new RouteNotFound());

        $this->transform('test')->shouldBeNull();
    }

    function it_sets_the_uri_path_form_router_container(
        RouterContainer $router,
        Generator $generator
    )
    {
        $router->getGenerator()->willReturn($generator);
        $this->beConstructedWith($router);

        $generator->generate('about', [])
            ->shouldBeCalled()
            ->willReturn('/pages/about');

        $this->transform('about')->shouldBeAnUriWithPath('/pages/about');
    }

    function it_accepts_query_params_in_options(
        ServerRequestInterface $request,
        RouterContainer $router,
        Generator $generator
    )
    {
        $this->prepareTest($request, $router, $generator);

        $this->transform('about', ['query' => ['foo' => 'bar']])
            ->shouldBeAnUriWithPath('/pages/about?foo=bar');
    }

    function it_can_reuse_host_name_from_context_request(
        ServerRequestInterface $request,
        RouterContainer $router,
        Generator $generator
    )
    {
        $this->prepareTest($request, $router, $generator);
        $this->transform('about', ['reuseHostName' => 1])
            ->shouldBeAnUriWithPath('https://localhost:12541/pages/about');
    }

    function it_can_reuse_the_request_query_params(
        ServerRequestInterface $request,
        RouterContainer $router,
        Generator $generator
    )
    {
        $this->prepareTest($request, $router, $generator);
        $this->transform('about', [
            'reuseParams' => 1,
            'query' => ['foo' => 'bar']
        ])
            ->shouldBeAnUriWithPath(
                '/pages/about?foo=bar&baz=bar'
            );
    }

    /**
     * Prepares the test
     *
     * @param ServerRequestInterface|Collaborator $request
     * @param RouterContainer|Collaborator $router
     * @param Generator|Collaborator $generator
     */
    private function prepareTest(
        ServerRequestInterface $request,
        RouterContainer $router,
        Generator $generator
    )
    {
        $this->beConstructedWith($router);
        $router->getGenerator()->willReturn($generator);
        $generator->generate('about', Argument::type('array'))
            ->shouldBeCalled()
            ->willReturn('/pages/about');
        $serverData = [
            'SCRIPT_NAME' => '/base/test.php',
            'HTTPS' => 'not-empty',
            'SERVER_PORT' => '12541',
            'SERVER_NAME' => 'localhost',
        ];
        $request->getServerParams()
            ->willReturn($serverData);
        $request->getQueryParams()->willReturn(
            ['foo' => 'bar', 'baz' => 'bar']
        );
        $this->setRequest($request);
    }

    public function getMatchers()
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
