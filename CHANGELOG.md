# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [v1.5.0] - 2023-02-22
### Adds
- PHP 8.X support
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

[Unreleased]: https://github.com/slickframework/web-stack/compare/v1.4.1...HEAD
[v1.4.1]: https://github.com/slickframework/web-stack/compare/v1.4.0...v1.4.1
[v1.4.0]: https://github.com/slickframework/web-stack/compare/v1.3.0...v1.4.0
[v1.3.0]: https://github.com/slickframework/web-stack/compare/v1.2.1...v1.3.0
[v1.2.1]: https://github.com/slickframework/web-stack/compare/v1.2.0...v1.2.1
[v1.2.0]: https://github.com/slickframework/web-stack/compare/v1.1.0-RC4...v1.2.0
[v1.1.0-RC4]: https://github.com/slickframework/web-stack/compare/v1.0.0...v1.1.0-RC4
[v1.0.0]: https://github.com/slickframework/web-stack/compare/2d2872...v1.0.0
