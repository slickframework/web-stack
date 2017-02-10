<?php

namespace spec\Slick\WebStack;

use PhpSpec\Exception\Example\FailureException;
use Psr\Http\Message\ResponseInterface;
use Slick\Di\ContainerInterface;
use Slick\Http\Server;
use Slick\WebStack\Application;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ApplicationSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(__DIR__.'/Services');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(Application::class);
    }

    function it_creates_dependency_container()
    {
        $this->getContainer()->shouldBeAContainerWithParent();
    }

    function it_runs_http_server_middleware_stack(
        Server $server,
        ResponseInterface $response
    ) {
      $server->run()->willReturn($response);
      $this->setHttpServer($server);
      $this->run()->shouldBe($response);
    }

    function getMatchers()
    {
        return [
            'beAContainerWithParent' => function($subject) {
                if (!$subject instanceof ContainerInterface) {
                    throw new FailureException(
                        "Returned value should be a ContainerInterface, but it was not!"
                    );
                }

                if (!$subject->has('isParent') || $subject->get('isParent') !== true) {
                    throw new FailureException(
                        "The returned container must have a parent that is created form ".
                        "services path passed when creating the application. This container ".
                        "was not properly created."

                    );
                }
                return true;
            }
        ];
    }
}
