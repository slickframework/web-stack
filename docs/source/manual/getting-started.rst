.. title:: Getting started: Slick Web Stack

Getting started
===============

Installation
------------

`slick/webstack` is a php 5.6+ library that you’ll have in your project development
environment. Before you begin, ensure that you have PHP 5.6 or higher installed.

You can install `slick/webstack` with all its dependencies through Composer. Follow
instructions on the `composer website`_ if you don’t have it installed yet.

You can use this Composer command to install `slick/webstack`:

.. code-block:: bash

    $ composer require slick/webstack

Application bootstrap
---------------------

It is very easy to get started with `slick/webstack` as it comes with a `slick/console` command
that will create the directory structure and set the very basic routes, services and controller
in place from where you can start building your web application.

First you will need to install the `slick/console` package with Composer. You should install it
as a `DEV` dependency as you do not need it when your application is in production.

You can use this Composer command to install `slick/console`:

.. code-block:: bash

    $ composer require slick/console --dev

Now lets execute the `init` command and get our application up and running:

.. code-block:: bash

    $ vendor/bin/slick init Infrastructure/WebUI

The base usage is `slick init <path>` where path is the directory where you application
source files will live in. The result will be `<vendor>\\<namespace>\\Infrastructure\\WebUI`
for all `slick/webstack` source files.

Set the application document root:

.. code-block:: bash

    Slick web application initialization
    ------------------------------------
    What's the application document root? (webroot):

Pressing enter will set the document root to the current working directory `/webroot` folder.
In this folder you should put all your web related files like `CSS` and `javascript` files.
You also have to set your http server document root to this folder.

.. code-block:: bash

    Please select the namespace to use (Slick\WebStack):
      [0] Slick\WebStack
      [1] Vendor\App
     >

Select the PSR-0/PSR-4 namespace you are working with and the application will be bootstrapped
for you!
If you point your browser to the server that is targeting the `/webroot` folder you should see
something like this:

.. figure:: firefox-index.png

.. _composer website: https://getcomposer.org/download/