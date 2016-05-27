# home-page.feature

  Feature: Load home page


    Scenario: Test home page
      Given I am on "/pages"
      Then I should see "Just the pages index!"