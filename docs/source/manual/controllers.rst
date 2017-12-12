Working with controllers
========================

Controllers have a key role in modern web applications as they are the orchestrators
of the actions a user can perform on the application's domain model.

Almost every request made to `slick/webstack` is handled by a controller method.

Creating a controller
---------------------

To create a controller you will need to implement the `ControllerInterface` or extend
from the abstract `Controller` class that has the basic implementation for a controller.

.. code-block:: php

    use Slick\WebStack\Controller;

    class MyController extends Controller
    {
        //Your code here
    }

It is also possible to create a controller by composition using a special `trait` that
has the implemented methods for `ControllerInterface` interface:

.. code-block:: php

    use Slick\WebStack\Controller\ControllerMethods;
    use Slick\WebStack\ControllerInterface;

    class MyController implements ControllerInterface
    {
        use ControllerMethods;
    }

.. _adding-data-to-view-section:

Adding data to the view
-----------------------

One of the main jobs of the controller is to grab/set the data that will be used in the
later rendering process.

The controller has a simple method that can be used to set that data. It is a very
versatile tool that can adapt to various usages:

.. code-block:: php

    use Slick\WebStack\Controller;

    class MyController extends Controller
    {

        public function index()
        {
            $this->set('theme', 'dark');    // Simple variable assignment

            $this->set([                    // Setting an entire associative array
                'small' => 10,
                'medium' => 50,
                'big' => 90
            ]);

            $user = $this->users->current();
            $this->set(compact('user'));    // Using compact to create an associative array
        }
    }

At rendering stage it will be available the following data:

.. code-block:: php

    array(
        'theme' => 'dark',
        'small' => 10,
        'medium' => 50,
        'big' => 90,
        'user' => User (
            'name' => 'John Doe',
            'email' => 'john.doe@exa,ple.com'
        )
    )

Context data
------------

When the dispatcher invokes the controller it will pass a context to it in order for you to
have access to the HTTP request and/or change the response.

With the context its possible to have access to the server parameters (usually post data), query parameters or
route parameters.

Query parameters
................

Lets assume that the user enters the following URL to the browser:
`http://localhost/some/action?page=2rows=10`

Now lets see how we can have access to those query parameters (`page` and `rows`) inside our controller:

.. code-block:: php

    use Slick\WebStack\Controller;

    class MyController extends Controller
    {

        public function action()
        {
            $page = $this->context->queryParam('page', 1);          // $page is 2
            $rows = $this->context->queryParam('rows', 12);         // $rows is 10
            $order = $this->context->queryParam('order', 'desc');   // $order is 'desc' because it uses
                                                                    // the default value
        }
    }

The API for the query parameters getter is very simple: you request a parameter by its name and if it
is set the value is returned, if not, the default value is returned. This way you can set all the parameters
to its defaults and apply them when they are set.

It is also possible to grab all the data as an associative array:

.. code-block:: php

    use Slick\WebStack\Controller;

    class MyController extends Controller
    {

        public function action()
        {
            $params = $this->context->queryParam(); // returns ['page' => '2', 'rows' => '10']
        }
    }

Parsed or posted parameters
...........................

Following the same principle you can also grab post data:

.. code-block:: php

    use Slick\WebStack\Controller;

    class MyController extends Controller
    {

        public function action()
        {
            $page = $this->context->postParam('name');
        }
    }

Or the entire post  like this:

.. code-block:: php

    use Slick\WebStack\Controller;

    class MyController extends Controller
    {

        public function action()
        {
            $page = $this->context->postParam(); // Something like ['name' => 'John Doe']
        }
    }

Some times you may have an object or other data when working with requests parsed body. For example
if you are developing a web service and you require that clients post JSON serialized objects, when
retrieving the data with `Context::postParam()` you will get the resulting object after deserialization.

.. code-block:: text

    POST /some/action HTTP/1.1
    Content-Type: application/json

    {"name": "John Doe"}

.. code-block:: php

    use Slick\WebStack\Controller;

    class MyController extends Controller
    {

        public function action()
        {
            $page = $this->context->postParam(); // Something like Object('name' => 'John Doe')
        }
    }

Checking request method
:::::::::::::::::::::::

When working with data it is common to check the request method in order to properly deal with that
same data. A `PUT` or a `PATCH` method may have different behaviors with the same data payload.
To check the request method do as follows:

.. code-block:: php

    use Slick\WebStack\Controller;

    class MyController extends Controller
    {

        public function action()
        {
            if ($this->context->requestIs('POST') {
                $page = $this->context->postParam();
            }
        }
    }


Route parameters
................

Routes can also have have parameters. Lets consider the following route definition:

.. code-block:: yaml

    blog-post:
        path: /posts/{slug}/{action}
        allows: [GET, POST]
        defaults:
          namespace: Name\Space
          controller: posts
          action: index

Has you can see this route will match against something like `http://localhost/posts/my-blog-post/edit`.
To have access to the `{slug}` parameter for example you can do the following:

.. code-block:: php

    use Slick\WebStack\Controller;

    class MyController extends Controller
    {

        public function edit()
        {
            $slug = $this->context->routeParam('slug'); // $slug will be 'my-blog-post'
        }
    }


.. warning::

    Remember that its always advisable that you filter all the input data. Query, post or route parameters are also a way
    to send data to the server. Those methods do not filter any data so its up to you to handle this security issues.

Handling responses
------------------

Some times you will need to set the response right in the controller. The common use case is when you need to
redirect the user to another page.

The controller context has a set of methods that let you manipulate the response and skip the late rendered process.


Redirect to other page
......................

Redirect the user to another page is very simple. Check it out:

.. code-block:: php

    use Slick\WebStack\Controller;

    class MyController extends Controller
    {
        public function action()
        {
            if ($this->context->requestIs('POST') {
                $page = $this->context->postParam();
                $this->context->redirect('home');       // This will change the response so that the browser
                                                        // will redirect to the page handled by route 'home'.
            }
        }
    }

`Context::redirect()` accept a route name and an optional associative array with route parameters. It also
accepts any string representing the path or URL of the page you want your user redirected to.

Setting response template
.........................

By default the template is created using the controller name and the handle method name.

For example the dispatcher is dispatching the handler `MyController::view()` it will look for the template
that is in `<templates-dir>/my-controller/view.twig`.

If you want to change the template you want the late render middleware to use do the following:

.. code-block:: php

    use Slick\WebStack\Controller;

    class MyController extends Controller
    {
        public function view()
        {
            $this->context->useTemplate('blog/posts/edit');
        }
    }

Note that the path is within the `<templates-dir>` directory and you don't need to set the `.twig` extension.

Adding request attributes
-------------------------

In some cases it is useful to add request attributes that can be used by a later or following middleware in the stack.
For those cases you can set the server request like this:

.. code-block:: php

    use Slick\WebStack\Controller;

    class MyController extends Controller
    {
        public function action()
        {
            $request = $this->context->request()->withAttribute('clearCache', true);
            $this->context->changeRequest($request);
        }
    }