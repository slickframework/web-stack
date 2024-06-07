# Slick Web Stack

[![Latest Version](https://img.shields.io/github/release/slickframework/web-stack.svg?style=flat-square)](https://github.com/slickframework/web-stack/releases)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/github/actions/workflow/status/slickframework/web-stack/continuous-integration.yml?style=flat-square)](https://github.com/slickframework/web-stack/actions/workflows/continuous-integration.yml)
[![Quality Score](https://img.shields.io/scrutinizer/g/slickframework/web-stack/master.svg?style=flat-square)](https://scrutinizer-ci.com/g/slickframework/web-stack?branch=master)
[![Quality Gate Status](https://sonarqube.fsilva.info/api/project_badges/measure?project=slickframework_web-stack_cdc9afed-0e9e-42ef-83a6-db3f934d9592&metric=alert_status&token=sqb_64f2dde98df223a9e07ac9e8e70fc5871f1eef72)](https://sonarqube.fsilva.info/dashboard?id=slickframework_web-stack_cdc9afed-0e9e-42ef-83a6-db3f934d9592)
[![Total Downloads](https://img.shields.io/packagist/dt/slick/webstack.svg?style=flat-square)](https://packagist.org/packages/slick/webstack)

``slick/webstack`` is a [PSR-15](https://www.php-fig.org/psr/psr-15/) HTTP middleware stack that can help you create
web applications or web services for HTTP protocol.

It offers a router, a dispatcher and view mechanism that returns [PSR-7](https://www.php-fig.org/psr/psr-7/) Responses for
HTTP Requests (usually through a web server).

You can change (add or remove) the HTTP stack by adding your own middleware making
this library very flexible and suitable for almost any HTTP handling needs.

This package is compliant with PSR-2 code standards and PSR-4 autoload standards.
It also applies the semantic version 2.0.0 specification.

## Install

Via Composer

``` bash
$ composer require slick/webstack
```

## Testing

We use [Behat](http://behat.org/en/latest/index.html) to describe features and for acceptance tests
and [PHPSpec](http://www.phpspec.net/) for unit testing.

``` bash
# unit tests
$ vendor/bin/phpinfo
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email slick.framework@gmail.com instead of using the issue tracker.

## Credits

- [Slick framework](https://github.com/slickframework)
- [All Contributors](https://github.com/slickframework/webstack/graphs/contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
