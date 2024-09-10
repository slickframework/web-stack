Feature: Application start a dispatch
  In order to use the web-stack
  As a developer
  I need to have a front controller setting up and starting the web application

  @webapp
  Scenario: Dispatch a simple controller
    Given I am on "/misc/check-status"
    Then I should see "Web stack application demo"

  @webapp
  Scenario: Pass a route parameter
    Given I am on "/misc/check-status/1234-4321-5678-8765"
    Then I should see "param: 1234-4321-5678-8765"