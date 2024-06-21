Feature: Application setup and start
  In order to use the web-stack
  As a developer
  I need to have a front controller setting up and starting the web application

  @webapp
  Scenario: Dispatch a simple controller
    Given I am on "/misc/check-status"
    Then I should see "It works!"