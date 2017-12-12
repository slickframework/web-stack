Layouts and views
=================

The `Slick\WebStack` package come with a render middleware that uses `Twig <https://twig.symfony.com/doc/1.x/templates.html>`_,
a modern template engine for PHP.

Create the template
-------------------

Templates are inflected from controller's name and calling method or action. For instance if you
are calling the following URL:

.. code-block:: text

    http://localhost/users/index

the dispatcher will use `Users::index()` and the renderer will look for `templates/users/index.twig` file to create
the HTTP response body.

The names are always lowercase, with dashes separating the words. Pleas check the following examples:

.. code-block:: text

    http://localhost/users/index        templates/users/index.twig
    http://localhost/userTypes          templates/user-types/index.twig
    http://localhost/users/partial_edit templates/users/partial-edit.twig

Change the templates path
.........................

.. important::

    This configuration change assumes that you have create your project using the project template form :ref:`getting-started-section`.
    If do not have this the configuration SHOULD be done were you initialize the template engine.

To change the templates home directory you need to edit the file `config/services/template-engine.php`

.. code-block:: php

    $services = [];

    // You can add your template paths here
    Template::addPath(APP_ROOT.'/templates');

    $services[TemplateEngineInterface::class] = '@template.engine';
    $services['template.engine'] = function (Container $container) {
        $template = new Template();
        $template->addExtension(new HtmlExtension($container->get('uri.generator')));
        return $template->initialize();
    };

    return $services;

Using twig
----------

This section is a simple summary of the basic features of `Twig <https://twig.symfony.com/doc/1.x/templates.html>`_
and is meant to just guide you to through the template creation on `Slick\WebStack`. For a full documentation on
this fantastic PHP template engine please go visit `Twig documentation page <https://twig.symfony.com/doc/1.x/>`_.

Variables
.........

The renderer passes variables to the templates (please see :ref:`adding-data-to-view-section`) for manipulation in
the template. Variables may have attributes or elements you can access, too. You can use a dot (.) to access
attributes of a variable (methods or properties of a PHP object, or items of a PHP array), or the so-called "subscript"
syntax ([]):

.. code-block:: html

    {{ foo.bar }}
    {{ foo['bar'] }}

    {# equivalent to the non-working foo.data-foo #}
    {{ attribute(foo, 'data-foo') }}

Setting variables
.................

You can assign values to variables inside code blocks. Assignments use the set tag:

.. code-block:: html

    {% set foo = 'foo' %}
    {% set foo = [1, 2] %}
    {% set foo = {'foo': 'bar'} %}

Filters
.......

Variables can be modified by filters. Filters are separated from the variable by a pipe symbol (|) and may have
optional arguments in parentheses. Multiple filters can be chained. The output of one filter is applied to the next.

.. code-block:: html

    {{ name|striptags|title }}
    {{ list|join(', ') }}

You can check a list of all `available filters <https://twig.symfony.com/doc/1.x/filters/index.html>`_.


Control structure
.................

A control structure refers to all those things that control the flow of a program - conditionals (i.e. if/elseif/else),
for-loops, as well as things like blocks. Control structures appear inside {% ... %} blocks.

For example, to display a list of users provided in a variable called users, use the for tag:

.. code-block:: html

    <h1>Members</h1>
    <ul>
        {% for user in users %}
            <li>{{ user.username|e }}</li>
        {% endfor %}
    </ul>

The if tag can be used to test an expression:

.. code-block:: html

    {% if users|length > 0 %}
    <ul>
        {% for user in users %}
            <li>{{ user.username|e }}</li>
        {% endfor %}
    </ul>
    {% endif %}

You can check a list of `all tags here <https://twig.symfony.com/doc/1.x/tags/index.html>`_

Including other Templates
.........................

The include function is useful to include a template and return the rendered content of
that template into the current one:

.. code-block:: html

    {{ include('sidebar.html') }}

By default, included templates have access to the same context as the template which includes
them. This means that any variable defined in the main template will be available in the
included template too:

.. code-block:: html

    {% for box in boxes %}
        {{ include('render_box.html') }}
    {% endfor %}

Template Inheritance
....................

The most powerful part of Twig is template inheritance. Template inheritance allows you to build
a base "skeleton" template that contains all the common elements of your site and defines blocks
that child templates can override.

Sounds complicated but it is very basic. It's easier to understand it by starting with an example.

Let's define a base template, `base.twig`, which defines a simple HTML skeleton document that you
might use for a simple two-column page:

.. code-block:: html

    <!DOCTYPE html>
    <html>
        <head>
            {% block head %}
                <link rel="stylesheet" href="style.css" />
                <title>{% block title %}{% endblock %} - My Webpage</title>
            {% endblock %}
        </head>
        <body>
            <div id="content">{% block content %}{% endblock %}</div>
            <div id="footer">
                {% block footer %}
                    &copy; Copyright 2011 by <a href="http://domain.invalid/">you</a>.
                {% endblock %}
            </div>
        </body>
    </html>

In this example, the block tags define four blocks that child templates can fill in. All the block
tag does is to tell the template engine that a child template may override those portions of the
template.

A child template might look like this:

.. code-block:: html

    {% extends "base.html" %}

    {% block title %}Index{% endblock %}
    {% block head %}
        {{ parent() }}
        <style type="text/css">
            .important { color: #336699; }
        </style>
    {% endblock %}
    {% block content %}
        <h1>Index</h1>
        <p class="important">
            Welcome to my awesome homepage.
        </p>
    {% endblock %}

The extends tag is the key here. It tells the template engine that this template "extends" another
template. When the template system evaluates this template, first it locates the parent. The extends
tag should be the first tag in the template.

Note that since the child template doesn't define the `footer` block, the value from the parent template
is used instead.

It's possible to render the contents of the parent block by using the parent function. This gives back
the results of the parent block:

.. code-block:: html

    {% block sidebar %}
        <h3>Table Of Contents</h3>
        ...
        {{ parent() }}
    {% endblock %}

.. important::

    Please note that this is a very short list of all Twig features and it serves only
    the propose of getting you with the basics of template construction on `Slick\WebStack`.
    You should visit the `Twig documentation site <https://twig.symfony.com/doc/1.x/templates.html>`_
    for a full list of features.