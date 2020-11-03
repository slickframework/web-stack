Defining routes
===============

One of the main goals of the `front controller <https://en.wikipedia.org/wiki/Front_controller>`_
pattern is to handle a request by delivering the execution to a specific controller that will
process the incoming request.

The ability to determine witch controller will handle the incoming request is done by a `Router`.

The `Router` has a collection of `Route` s with request target patterns and the corresponding
`Controller`'s names, handler method and arguments to be called by the `Dispatcher`.

The `RouterMiddleware` will try to match the incoming request against the `Route` 's collection
to determine the one that will be used by the dispatcher.

Create the RouterContainer
--------------------------

.. important::

    If you have created your project using the project template form :ref:`getting-started-section`
    you can skip the creation of the `RouterContainer` as this is already done.
    There's also a routes file already created in `config/routes.yml` that you will need to edit
    to match your requirements.


To create a `RouterContainer` you need to do the following:

.. code-block:: php

    use Aura\Router\RouterContainer;
    use Slick\WebStack\Http\Router\Builder\RouteFactory;
    use Slick\WebStack\Http\Router\RouteBuilder;
    use Slick\WebStack\Http\RouterMiddleware;
    use Slick\WebStack\Router\Parsers\PhpYmlParser;


    $routeBuilder = new RouteBuilder(__DIR__.'/routes.yml', new PhpYmlParser(), new RouteFactory());
    $routerContainer = new RouterContainer();
    $routeBuilder->register($routerContainer);


It not so simple, I admit! But putting it in simple words you need a file with your route definitions, a
parser for that file (YAML in this case) and a route factory that will create Routes from the file definitions.

Then you register it as a route builder in the routes container.

Every time you try to match a request it will recreate the routes you define before it.

.. note::

    We create a simple route builder to the very well done `Aura.Router <https://github.com/auraphp/Aura.Router>`_ package.
    The `Route`, `Matcher` and `RouterContainer` are objects that we use *as is* from that package.

Defining routes
---------------

Routes are defined in a routes file. A simple example could look like this:

.. code-block:: yaml

    routes:
        home:
            method: GET
            path: /
            defaults:
                action: home
        catchall:
            allows: [POST, GET]
            path: "{/controller,action}"
            wildcard: args
    defaults:
        namespace: Controller
        action: index
        controller: pages

Default values
..............

The `defaults` entry is where we define the properties that the dispatcher will use to handle the request.
Each route has a `defaults` entry or it can inherit from the global defaults.

There are 3 mandatory keys in this entry:

* **namespace:** The namespace where your controller class lives in;
* **controller:**  The controller name. This string will be converted to a regular class name.

  * *pages* will be converted to `Pages`;
  * *my-pages* will be converted to `MyPages`;
  * *otherPages* will be converted to `OtherPages`;

* **action:** The method that will handle the request inside the controller

  * *index* will be used as is;
  * *filtered-index* will be converted to `filteredIndex`

Lets take a look to the `home` route defined in the example file:

.. code-block:: yaml

    routes:
        home:
            method: GET
            path: /
            defaults:
                action: home

It only defines the `action` default key but when it matches the result controller and method to be called
will be

.. code-block:: php

    Controller\Pages::home();

Route list (router)
...................

The route list or router is a collection of named routes that are defined in the `routes` entry.

.. important::

    The order in witch you define the routes in the routes file is very important. The matcher will
    iterate over the collection and will return the first match. So you need to place the more generic
    definition at the bottom and the more specific ones at the top.

Route definition
................

A route has the following keys:

* **path:** The pattern that will be used to match against the request target;
* **method:** The request method. One of GET, POST, PATCH, PUT, DELETE, HEAD...
* **defaults:** Information that will be used to dispatch the request;
* **allows:** Used to define more then one method. Example [GET, POST];
* **auth:** A key value list of properties that can be used for authentication proposes;
* **tokens:** A key value list of properties for placeholder token names and regexes;
* **accepts:** A list of content types that the route handler can be expected to return.;
* **host:** To limit a route to specific hosts;
* **wildcard:** To allow arbitrary trailing path segments on a route;

Placeholder tokens
~~~~~~~~~~~~~~~~~~

When you add a {token} placeholer in the path, it uses a default regular expression of ([^/]+).
Essentially, this matches everything except a slash, which of course indicates the next path segment.

To define custom regular expressions for placeholder tokens, use the `tokens` method.

.. code-block:: yaml

    routes:
        blog.read:
            method: GET
            path: /blog/{id}{format}
            tokens:
                id: '\d+'
                format: '(\.[^/]+)?'
            defaults:
                format: '.html'

The Route object does not predefine any tokens for you. One that you may find useful is a {format}
token, to specify an optional dot-format extension at the end of a file name.

If no default value is specified for a placeholder token, the corresponding attribute value will
be `null`. To set your own default values, add it to the `defaults` entry.

Optional placeholder tokens
~~~~~~~~~~~~~~~~~~~~~~~~~~~

Sometimes it is useful to have a route with optional placeholder tokens for attributes. None,
some, or all of the optional values may be present, and the route will still match.

To specify optional attributes, use the notation {/attribute1,attribute2,attribute3} in the path.
For example:


.. code-block:: yaml

    routes:
        archive:
            method: GET
            path: /archive{/year,month,day}
            tokens:
                year: '\d{4}'
                month: '\d{2}'
                day: '\d{2}'

Note that the leading slash separator is inside the placeholder token, not outside.

With that, the following paths will all match the 'archive' route, and set the attribute values accordingly:

.. code-block:: text

    /archive : ['year' => null, 'month' => null, 'day' = null]
    /archive/1979 : ['year' => '1979', 'month' => null, 'day' = null]
    /archive/1979/11 : ['year' => '1979', 'month' => '11', 'day' = null]
    /archive/1979/11/07 : ['year' => '1979', 'month' => '11', 'day' = '07']

.. important::

    Optional attributes are sequentially optional. This means that, in the above example, you cannot have a
    "day" without a "month", and you cannot have a "month" without a "year".
    You can have only one set of optional attributes in a route path.
    Optional attributes belong at the end of a route path. Placing them elsewhere may result in unexpected behavior.

Wildcard Attributes
~~~~~~~~~~~~~~~~~~~

Sometimes it is useful to allow the trailing part of the path be anything at all. To allow arbitrary trailing
path segments on a route, add the `wildcard` entry. This will let you specify the attribute name under
which the arbitrary trailing values will be stored.

.. code-block:: yaml

    routes:
        wild:
            method: GET
            path: /wild
            wildcard: card

All slash-separated path segments after the `/wild` path will be captured as an array in the in wildcard
attribute. For example:

.. code-block:: text


    /wild : ['card' => []]
    /wild/foo : ['card' => ['foo']]
    /wild/foo/bar : ['card' => ['foo', 'bar']]
    /wild/foo/bar/baz : ['card' => ['foo', 'bar', 'baz']]

Wildcards as arguments
~~~~~~~~~~~~~~~~~~~~~~

There is a special case that you can use the wildcard entry to pass arguments to the calling controller handler method:

.. code-block:: yaml

    routes:
        catchall:
            allows: [POST, GET]
            path: "{/controller,action}"
            wildcard: args

A request with the target `/posts/read/23` will be dispatched as:

.. code-block:: php

    Controller\Posts::read(23);

Nested definition files
.......................

You can organize your route definitions in multiple files that you can add to the main routes file.

For example: if you want to have a group of route definitions for a *blog* resource you can do like this:

.. code-block:: yaml

    routes:
        blog: blog/routes
        home:
            method: GET
            path: /
            defaults:
                action: home
        catchall:
            allows: [POST, GET]
            path: "{/controller,action}"
            wildcard: args
    defaults:
        namespace: Controller
        action: index
        controller: pages

Please note the route named `blog`. It has just the name of the routes file to import into that position.
The `RouteBuilder` will look for the file in `config/blog/routes.yml` and it will throw an exception if
the file is not found.

The `config/blog/routes.yml` could be something like:

.. code-block:: yaml

    blog.read:
        method: GET
        path: /blog/{id}{format}
        tokens:
            id: '\d+'
            format: '(\.[^/]+)?'
        defaults:
            format: '.html'

.. note::

    **Nested files** feature is only available with version `v1.2.0` or higher.
