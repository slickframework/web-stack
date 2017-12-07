Feature: Flash messages
  In order to display a flash message to the user
  As a developer
  I want to set a flash message in a controller before redirecting it
  to other page

  Scenario: Flash message on redirect
    Given I am on "/pages/process"
    Then I should see "You have been redirected"