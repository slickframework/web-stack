# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [v2.0.9] - 2025-02-02
### Adds
- `AbstractApplication::container()` to allow running applications to retrieve the dependency
  container and interact with available dependencies.

## [v2.0.8] - 2024-11-25
### Fixes
- Loading modules fail for custom modules

## [v2.0.7] - 2024-11-17
### Fixes
- Redirect location after login missing the full URI

## [v2.0.6] - 2024-11-13
### Adds
- Access token authenticator
### Fixes
- Removed the need to return a 401 response when no authenticator support the request
- The entry point is checked when checking grant in current session.

## [v2.0.5] - 2024-11-04
### Fixes
- Context passed to router was not created properly, ending in routing failures.

## [v2.0.4] - 2024-09-22
### Fixes
- Enabling modules that use uppercase prefix namespaces.

## [v2.0.3] - 2024-09-22
### Added
- Module system to help initializing modules;
- Module command line tools;
- Security module to allow easy authentication and authorization flows;
- Security profiles based on request path regular expression match;
- Router debug console command
- Console script with command loader that searches for Symfony console commands and registers them as
  applications;
- Refactor dispatcher as a module;
- Logging support PSR-03

## [v1.5.3] - 2023-03-07
### Fixes
- Context getRouteParam(), getQueryParam() and getPostParam() now accept ``mixed`` as default value
  and returns ``mixed`` as well.

## [v1.5.2] - 2023-02-23
### Removes
- ``slick/configuration`` dependency as it isn't used anywhere.
- 
## [v1.5.1] - 2023-02-23
### Adds
- Controller dispatcher and middleware returns the controller method response is exists.
- PHP 8.X support
### Deprecates
- ``ControllerContextInterface::disableRendering()`` Controller shouldn't control what is handled by
  a further middleware in stack.
- ``ControllerContextInterface::useTemplate()`` Controller shouldn't control what is handled by
  a further middleware in stack.
- ``ControllerContextInterface::setResponse()`` Use the controller handler method to return a PSR-7 response message.
- ``ControllerContextInterface::response()`` Use the controller handler method to return a PSR-7 response message.
- ``ControllerContextInterface::handlesResponse()`` Use the controller handler method to return a PSR-7 response message.
- ``RendererMiddleware`` User should handle/install rendering middleware.
- ``FlashMessagesMiddleware`` User should handle session messages.

### Changes
- ``slick/template`` version to v2.0.0 for better PHP 8.X support
### Removes
- PHP 7.X support


## [v1.4.1] - 2020-11-04
### Fixes
- Composer lock file error.

## [v1.4.0] - 2020-11-03
### Added
- Routes parser interface
- Support for PECL yaml parsing
### Changed
- Route builder now uses the `RoutesParser` interface to parse route definitions

## [v1.3.0] - 2020-04-28
### Changed
- Support new versions of `slick/http`
### Removed
- Drop support for PHP < 7.2 

## [v1.2.2] - 2020-03-06
### Fixes
- `ControllerContextInterface::useTemplate()` has no effect
- Documentation search javascript tool

## [v1.2.1] - 2020-03-04
### Changed
- Readme package description and usage
- License year update
- Dependencies update
### Added
- Contributing information file

## [v1.2.0] - 2019-03-20
### Added
- Nested route files
### Changed
- Sphinx documentation library was updated
- Upgrade ``slick/di`` to version v2.5.0 
- Upgrade ``slick/http`` to version v2.0.0 (stable) 
### Removed
- Support PHP <= 7.0

## [v1.1.0-RC4] - 2018-01-03
### Added
- ``ControllerContextInterface::routeParam()`` to retrieve route parameters
- ``ControllerContextInterface::requestIs()`` check the request method
- ``ControllerContextInterface::changeRequest()`` to change or update the request passed to the next middleware
- ``ControllerContextInterface::handlesResponse()`` check if it will stop the stack and should return a response
### Changed
- Context changed some method names to be more verbose
- ``ControllerContextInterface::getPost()`` changed to ``ControllerContextInterface::postParam()``
- ``ControllerContextInterface::getQuery()`` changed to ``ControllerContextInterface::queryParam()``
- ``ControllerContextInterface::setView()`` changed to ``ControllerContextInterface::useTemplate()``
- ``ControllerContextInterface::getRequest()`` changed to ``ControllerContextInterface::request()``
- ``ControllerContextInterface::getRequest()`` changed to ``ControllerContextInterface::request()``
- ``ControllerContextInterface::getResponse()`` changed to ``ControllerContextInterface::response()``
### Removed
- ``ControllerContextInterface::register()`` was removed and the context is instantiated with a ``ServerReqeustInterface``
  object as a dependency passed to the constructor.    


## [v1.0.0] - 2017-09-05
### Added
- First release 

[Unreleased]: https://github.com/slickframework/web-stack/compare/v2.0.9...HEAD
[v2.0.9]: https://github.com/slickframework/web-stack/compare/v2.0.8...v2.0.9
[v2.0.8]: https://github.com/slickframework/web-stack/compare/v2.0.7...v2.0.8
[v2.0.7]: https://github.com/slickframework/web-stack/compare/v2.0.6...v2.0.7
[v2.0.6]: https://github.com/slickframework/web-stack/compare/v2.0.5...v2.0.6
[v2.0.5]: https://github.com/slickframework/web-stack/compare/v2.0.4...v2.0.5
[v2.0.4]: https://github.com/slickframework/web-stack/compare/v2.0.3...v2.0.4
[v2.0.3]: https://github.com/slickframework/web-stack/compare/v1.5.3...v2.0.3
[v1.5.3]: https://github.com/slickframework/web-stack/compare/v1.5.2...v1.5.3
[v1.5.2]: https://github.com/slickframework/web-stack/compare/v1.5.1...v1.5.2
[v1.5.1]: https://github.com/slickframework/web-stack/compare/v1.4.1...v1.5.1
[v1.4.1]: https://github.com/slickframework/web-stack/compare/v1.4.0...v1.4.1
[v1.4.0]: https://github.com/slickframework/web-stack/compare/v1.3.0...v1.4.0
[v1.3.0]: https://github.com/slickframework/web-stack/compare/v1.2.1...v1.3.0
[v1.2.1]: https://github.com/slickframework/web-stack/compare/v1.2.0...v1.2.1
[v1.2.0]: https://github.com/slickframework/web-stack/compare/v1.1.0-RC4...v1.2.0
[v1.1.0-RC4]: https://github.com/slickframework/web-stack/compare/v1.0.0...v1.1.0-RC4
[v1.0.0]: https://github.com/slickframework/web-stack/compare/2d2872...v1.0.0
