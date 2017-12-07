# Changelog

All Notable changes to `Slick/WebStack` will be documented in this file.

## [Unreleased]

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


## [v1.0.0] - 2017-06-24

### Added
- First release 

[Unreleased]: https://github.com/slickframework/web-stack/compare/v1.0.0...HEAD
[v1.0.0]: https://github.com/slickframework/configuration/compare/v1.0.0...master