# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

## [Unreleased]
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

[Unreleased]: https://github.com/slickframework/web-stack/compare/v1.1.0-RC4...HEAD
[v1.1.0-RC4]: https://github.com/slickframework/web-stack/compare/v1.0.0...v1.1.0-RC4
[v1.0.0]: https://github.com/slickframework/web-stack/compare/2d2872...v1.0.0
