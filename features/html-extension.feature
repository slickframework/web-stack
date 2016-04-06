# html-extension.feature
  Feature: HTML extension to twig
    In order to help construct twig files
    As  developer
    I want to have a url() method that take a path as input and will output
      the correct url to in context with the request.

    In order to help construct twig files
    As  developer
    I want to have a addCss() method that take a file name and an optional path
    and a full css style tag will be printed

  Scenario: Print full url
    When I request page "/url/full"
    Then response should contain "http://example.com"

  Scenario: Print as is
    When I request page "/url/asIs"
    Then response should contain "some/path"

  Scenario: Print as known route
    When I request page "/url/home"
    Then response should contain "/"

  Scenario: Print css style tag
    When I request page "/url/home"
    Then response should contain '<link href="/css/bootstrap.min.css" rel="stylesheet">'

  Scenario: Print css style tag
    When I request page "/url/home"
    Then response should contain '<script src="/javascripts/bootstrap.min.js"></script>'