<?php

namespace spec\Slick\WebStack\Http\Renderer\TemplateExtension;

use PhpSpec\Exception\Example\FailureException;
use Slick\WebStack\Http\Renderer\TemplateExtension\HtmlExtension;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Slick\WebStack\Service\UriGeneratorInterface;
use Slick\Template\EngineExtensionInterface;

/**
 * Html Extension Spec
 *
 * @package spec\Slick\WebStack\Http\Renderer\TemplateExtension
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
class HtmlExtensionSpec extends ObjectBehavior
{
    function let(UriGeneratorInterface $generator)
    {
        $this->beConstructedWith($generator);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(HtmlExtension::class);
    }

    function its_a_template_engine_extension()
    {
        $this->shouldBeAnInstanceOf(EngineExtensionInterface::class);
    }

    function it_defines_a_template_function_for_url_output()
    {
        $this->getFunctions()->shouldHaveAFunctionNamed('url');
    }

    function it_uses_an_uri_generator_to_output_the_url(
        UriGeneratorInterface $generator
    )
    {
        $generator->generate('test', [])
            ->shouldBeCalled()
            ->willReturn('/test');

        $this->beConstructedWith($generator);

        /** @var \Twig_SimpleFunction $func */
        $func = $this->getFunctions()['url'];
        call_user_func_array($func->getCallable(), ['test']);
    }

    function it_defines_a_template_function_for_css_include_tag()
    {
        $this->getFunctions()->shouldHaveAFunctionNamed('addCss');
    }

    function it_uses_an_uri_generator_to_output_the_css_url(
        UriGeneratorInterface $generator
    )
    {
        $generator->generate('css/test.css', [])
            ->shouldBeCalled()
            ->willReturn('/css/test.css');

        $this->beConstructedWith($generator);

        /** @var \Twig_SimpleFunction $func */
        $func = $this->getFunctions()['addCss'];
        call_user_func_array($func->getCallable(), ['test.css']);
    }

    function it_defines_a_template_function_for_add_javascript_tag()
    {
        $this->getFunctions()->shouldHaveAFunctionNamed('addJs');
    }

    function it_uses_an_uri_generator_to_output_the_js_url(
        UriGeneratorInterface $generator
    )
    {
        $generator->generate('js/test.js', [])
            ->shouldBeCalled()
            ->willReturn('/js/test.js');

        $this->beConstructedWith($generator);

        /** @var \Twig_SimpleFunction $func */
        $func = $this->getFunctions()['addJs'];
        call_user_func_array($func->getCallable(), ['test.js']);
    }

    public function getMatchers()
    {
        return [
            'haveAFunctionNamed' => function ($subject, $name) {
                $type = gettype($subject);
                if (!is_array($subject)) {
                    throw new FailureException(
                        "Expected an array of functions, but got {$type}."
                    );
                }

                $found = null;
                foreach ($subject as $function) {
                    if (!$function instanceof \Twig_SimpleFunction) {
                        continue;
                    }

                    if ($function->getName() == $name) {
                        return true;
                    }
                }

                throw new FailureException(
                    "Returned function list does not have a function ".
                    "named '{$name}'."
                );
            }
        ];
    }
}
