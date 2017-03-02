<?php

/**
 * This file is part of WebStack
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Features\App\Controller;

use Interop\Container\ContainerInterface as InteropContainer;
use Slick\Di\ContainerInjectionInterface;
use Slick\WebStack\Controller;
use Slick\WebStack\Service\FlashMessages;

/**
 * Messages
 *
 * @package Features\App\Controller
 * @author  Filipe Silva <filipe.silva@sata.pt>
 */
class Messages extends Controller implements ContainerInjectionInterface
{

    /**
     * @var FlashMessages
     */
    private $flashMessages;

    /**
     * Messages
     *
     * @param FlashMessages $flashMessages
     */
    public function __construct(FlashMessages $flashMessages)
    {
        $this->flashMessages = $flashMessages;
    }

    /**
     * Instantiates a new instance of this class.
     *
     * This is a factory method that returns a new instance of this class. The
     * factory should pass any needed dependencies into the constructor of this
     * class, but not the container itself. Every call to this method must return
     * a new instance of this class; that is, it may not implement a singleton.
     *
     * @param InteropContainer $container
     *   The service container this instance should use.
     *
     * @return self|Messages
     */
    public static function create(InteropContainer $container)
    {
        return new Messages(
            $container->get(FlashMessages::class)
        );
    }

    public function setMessages()
    {
        $this->flashMessages->addError('Test!');
        $this->flashMessages->addInfo('Test!');
        $this->flashMessages->addInfo('Test2!');
        $this->flashMessages->addWarning('Test!');
        $this->flashMessages->addSuccess('Test!');
        $this->context->redirect('messages/show');
    }

    public function show()
    {

    }


}
