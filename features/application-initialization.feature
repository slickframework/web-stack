Feature: Application initialization
  In order to initialise a web application
  As a developer
  I want to use a CLI command that will create the structure and
    bootstrap a new application.

  Scenario: Application bootstrap with command
    Given I run init command with "/"
    And I set "features/app/webroot" for document root
    And I choose "Features\App" for namespace
    When I execute the command
    Then I am on the homepage
    And I should see "Web stack application!"