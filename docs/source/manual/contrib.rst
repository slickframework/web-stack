.. title:: Contributing: Slick Framework

Contributing
============

Contributions are **welcome** and will be fully **credited**.
We accept contributions via Pull Requests on `Github`_.

Pull requests
-------------
* `PSR-2 Coding Standard`_ - The easiest way to apply the conventions is to install `PHP Code Sniffer`_.
* **Add tests!** - Your patch won't be accepted if it doesn't have tests.
* **Document any change in behaviour** - Make sure the README.md and any other relevant documentation are kept up-to-date.
* **Consider our release cycle** - We try to follow `SemVer v2.0.0`_. Randomly breaking public APIs is not an option.
* **Create feature branches** - Don't ask us to pull from your master branch.
* **One pull request per feature** - If you want to do more than one thing, send multiple pull requests.
* **Send coherent history** - Make sure each individual commit in your pull request is meaningful. If you had to make multiple intermediate commits while developing, please squash them before submitting.

Running tests
-------------
We use `Behat`_ to describe features and and for acceptance tests
and `PHPUnit`_ for integration and unit testing.

.. code-block:: bash

    # unit tests
    $ vendor/bin/phpspec run

    # acceptance tests
    $ vendor/bin/behat

Security
--------

If you discover any security related issues, please email
`silvam.filipe@gmail.com <mailto:silvam.filipe@gmail.com>`_ instead of using the issue tracker.

.. _Github: https://github.com/slickframework/di
.. _PSR-2 Coding Standard: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md
.. _PHP Code Sniffer: http://pear.php.net/package/PHP_CodeSniffer
.. _SemVer v2.0.0: http://semver.org
.. _Behat: http://behat.org/en/latest/index.html
.. _PHPUnit: https://phpunit.de/
