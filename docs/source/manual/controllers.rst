Working with controllers
========================

Controllers have a key role in modern web applications as they are the orchestrators
of the actions a user can perform on the application's domain model.

Almost every request made to `slick/webstack` is handled by a controller method.

Creating a simple controller
----------------------------

To create a controller you will need to implement the `ControllerInterface` or extend
from the abstract `Controller` class that has the basic implementation for a controller.

.. code-block:: php

    use Slick\WebStack\Controller;

    class MyController extends Controller
    {
        //Your code here
    }

