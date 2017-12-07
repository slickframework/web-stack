Displaying flash messages
=========================

A flash message is used in order to keep a message in session through one or
several requests of the same user. By default, it is removed from session after
it has been displayed to the user. Flash messages are usually used in
combination with HTTP redirections, because in this case there is no view, so
messages can only be displayed in the request that follows redirection.

Add it to your controller
-------------------------

In order to use the `FlashMessages` service you will need to inject it to your
controller. You can achieve this by specifying the dependency in your controller
constructor.

.. code-block:: php

    use Slick\WebStack\Controller;
    use Slick\WebStack\Service\FlashMessages;

    class MyController extends Controller
    {

        private $flashMessages;

        public function __construct(FlashMessages $flashMessages)
        {
            $this->flashMessages = $flashMessages;
        }
    }

Now you can use it to set the messages to the users like::

    class MyController extends Controller implements ContainerInjectionInterface
    {
        ...

        public function save()
        {
            ...
            $this->flashMessages->addSuccess('Data successfully saved!');
            $this->context->redirect('home');
        }
    }

Show the messages
-----------------

To display the messages in the view you need to include the `flash/messages.twig`
template that come with `slick/webstack`:

.. code-block:: html

    ...
    <body>
        <div class="container">
            {% include "flash/messages.twig" %}
            ...
        </div>
    </body>

Flash messages API
------------------

.. php:namespace:: Slick\WebStack\Service

.. php:class:: FlashMessages

    .. php:method:: addInfo($message)

        Adds an informational message to the messages stack

        :param string $message: The message to display
        :returns: The FlashMessages service itself. Useful for other method calls.

    .. php:method:: addSuccess($message)

        Adds a success message to the messages stack

        :param string $message: The message to display
        :returns: The FlashMessages service itself. Useful for other method calls.

    .. php:method:: addWarning($message)

        Adds a warning message to the messages stack

        :param string $message: The message to display
        :returns: The FlashMessages service itself. Useful for other method calls.

    .. php:method:: addError($message)

        Adds an error message to the messages stack

        :param string $message: The message to display
        :returns: The FlashMessages service itself. Useful for other method calls.