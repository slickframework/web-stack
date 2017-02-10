.. title:: Getting started: Slick Framework

Getting started
===============

Introduction
------------

Dependency injection is a concept that has been talked about all over the web.
You probably have done it without knowing that is called dependency injection.
Simply put the next line of code can describe what it is::

    $volvo = new Car(new Engine());

Above, ``Engine`` is a dependency of ``Car``, and ``Engine`` was injected into
``Car``. If you are not familiar with Dependency Injection please read this
Fabien Pontencier's `great series about Dependency injection`_.

Dependency injection and dependency injection containers are tow different
things. Dependency injection is a design pattern that implements
`inversion of control`_ for resolving dependencies. On the other hand
Dependency Injection Container is a tool that will help you create, reuse
and inject dependencies.

A dependency container can also be used to store object instances that you
create and values that you may need to use repeatedly. A good example of this
are configuration settings.

Basic usage
-----------

To create a dependency container we need to create at least a ``services.php``
file with all our dependency definitions::

    use Slick\Configuration\Configuration:
    use Slick\Di\Definition\ObjectDefinition;

    /**
     * Dependency injection object definition example
     */
    return [
        'config' => function() {
            return Configuration::get('config');
        },
        'engineService' => ObjectDefinition::create(Engine::class)
            ->with('@config')
            ->call('setMode')->with('simple')
    ];

Now to build the dependency container we need to use the ``ContainerBuilder`` factory class like this::

    use Slick\Di\ContainerBuilder;

    $definitionsFile = __DIR__ . '/services.php';
    $container = (new ContainerBuilder($definitionsFile))->getContainer();

With that, we are ready to create and inject dependencies with our container::

    class Car
    {
        /**
     * @var EngineInterface
     */
        protected $engine;

        /**
     * @inject engineService
     *
     * @return self
     */
        public function setEngine(EngineInterface $engine)
        {
            $this->engine = $engine;
            return $this;
        }
    }

    $myCar = $container->get(Car::class);

Installation
------------

`slick/di` is a php 5.6+ library that you’ll have in your project development
environment. Before you begin, ensure that you have PHP 5.6 or higher installed.

You can install `slick/di` with all its dependencies through Composer. Follow
instructions on the `composer website`_ if you don’t have it installed yet.

You can use this Composer command to install `slick/di`:

.. code-block:: bash

    $ composer require slick/di



.. _composer website: https://getcomposer.org/download/
.. _great series about Dependency injection: http://fabien.potencier.org/what-is-dependency-injection.html
.. _inversion of control: https://en.wikipedia.org/wiki/Inversion_of_control