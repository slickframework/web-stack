<?php

/**
 * This file is part of slick/web_stack package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Features\App\Controller;

use Slick\WebStack\Controller\ControllerMethods;
use Slick\WebStack\ControllerInterface;
use Slick\WebStack\Service\FlashMessages;

/**
 * Pages controller
 *
 * @package Features\App\Controller
 */
class Pages implements ControllerInterface
{
    use ControllerMethods;

    /**
     * @var FlashMessages
     */
    private $messages;

    /**
     * Pages constructor.
     * @param FlashMessages $messages
     */
    public function __construct(FlashMessages $messages)
    {
        $this->messages = $messages;
    }

    public function home()
    {
        $this->set('message', 'Web stack application!');
    }

    public function process()
    {
        $this->messages->addInfo('You have been redirected');
        $this->context->redirect('home');
    }
}