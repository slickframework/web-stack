Feature: Child route definition files
  In order to better organize my route definitions
  As a developer
  I want to have multiple/nested files with route definitions

  Scenario: request nested route
    Given I am on "article/23"
    Then I should see "Argument was: 23"
