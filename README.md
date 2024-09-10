# Slick Web Stack

[![Latest Version](https://img.shields.io/github/release/slickframework/web-stack.svg?style=flat-square)](https://github.com/slickframework/web-stack/releases)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/github/actions/workflow/status/slickframework/web-stack/continuous-integration.yml?style=flat-square)](https://github.com/slickframework/web-stack/actions/workflows/continuous-integration.yml)
[![Quality Score](https://img.shields.io/scrutinizer/g/slickframework/web-stack/master.svg?style=flat-square)](https://scrutinizer-ci.com/g/slickframework/web-stack?branch=master)
[![Total Downloads](https://img.shields.io/packagist/dt/slick/webstack.svg?style=flat-square)](https://packagist.org/packages/slick/webstack)

The `slick/webstack` library provides a PSR-15 HTTP middleware stack designed for building
web applications or services that use the HTTP protocol. It includes a router, security
features, and a dispatcher that generates PSR-7 compliant responses to incoming HTTP
requests, typically routed through a web server.

One of the key strengths of this library is its flexibility. You can easily modify the
HTTP middleware stack by adding or removing middleware components to suit your specific
needs, making it an adaptable solution for various HTTP handling scenarios.

This package is compliant with PSR-2 code standards and PSR-4 autoload standards.
It also applies the semantic version 2.0.0 specification.

## Install

Via Composer

``` bash
$ composer require slick/webstack
```

## Usage

For a complete manual using this library, please refer to
[Slick Documentation Web Site](https://www.slick-framework.com).

## Testing

We use PHPUnit for unit testing

``` bash
# unit tests
$ vendor/bin/phpunit
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email slick.framework@gmail.com
instead of using the issue tracker.

## Credits

- [Slick framework](https://github.com/slickframework)
- [All Contributors](https://github.com/slickframework/webstack/graphs/contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
