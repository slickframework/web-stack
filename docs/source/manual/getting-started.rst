.. title:: Getting started: Slick Web Stack

.. _getting-started-section:

Getting started
===============

``slick/webstack package`` is a PSR-7 HTTP middleware stack that can help you create
web applications or web services for HTTP protocol.
It offers a router, dispatcher and view mechanism that returns PSR-7 Responses for
HTTP Requests (usually through a web server).

You can change (add or remove) the HTTP stack by adding your own middleware making
this library very flexible and suitable for almost any HTTP handling needs.

Installation
------------

`slick/webstack` is a php 5.6+ library that you’ll have in your project development
environment. Before you begin, ensure that you have PHP 5.6 or higher installed.

You can install `slick/webstack` with all its dependencies through Composer. Follow
instructions on the `composer website`_ if you don’t have it installed yet.

You can use this Composer command to get started:

.. code-block:: bash

    $ composer create-project slick/webapp:^2.0@dev ./my-project



.. _composer website: https://getcomposer.org/download/

Developing environment
----------------------

The project template used in the above command is prepared to run with *docker* and
*docker-compose*.

There are other options like *vagrant* or even a (M|X|W|L)AMP stack installed on your
developing environment.

We choose to use the *docker* as it is very flexible and lightweight. It gives you
the possibility to configure your environment with a very little afford.

We also use a PHP image that comes with some useful tools like composer and xdebug.

All the examples that we used in this site were made with docker-compose and for that
we create a set of *nix compatible alias to help you with the command line:

.. code-block:: bash

    # Docker and docker-compose alias

    function docker-host {
      type docker-machine >/dev/null 2>&1 && docker-machine ip $DOCKER_MACHINE_NAME || \
        ip a | sed -En 's/.*inet (addr:)?((10|192)(\.[0-9]*){3}).*/\2/p' | head -n1
    }

    function dc-port {
      echo `docker-compose port $1 $2 | cut -d: -f2`
    }

    function dc-open {
      local _open=open
      type xdg-open >/dev/null 2>&1 && _open=xdg-open
      $_open http://${DC_HOST:-`docker-host`}:`dc-port ${1:-web} ${2:-80}`$3 >/dev/null 2>&1
    }

    alias dc="docker-compose"
    alias dc-run="dc run --rm"
    alias dc-php="dc-run php gosu www-data php "
    alias dc-composer='dc-run -e USE_XDEBUG=no php gosu www-data composer'
    alias dc-phpspec='dc-run -e USE_XDEBUG=no php gosu www-data vendor/bin/phpspec'
    alias dc-behat='dc-run -e USE_XDEBUG=no php gosu www-data vendor/bin/behat'

Just copy the above code to a startup script of your favorite shell (usually ~/.profile, ~/.bashrc, etc...)

Fire it up
----------

In order to run the application (assuming you already set your environment to use the suggested alias above)
you just need to initiate the container that is configured in the template project lik this:

.. code-block:: bash

    dc up -d

This command will start a PHP:7.1 container and link your working directory to the server webroot path so
that any change in the files you are working is available to the apache running in the container.

Lets open a browser pointing to our running container:

.. code-block:: bash

    dc-open php

You should get the welcome page from template project.

.. figure:: firefox-index.png