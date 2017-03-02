Feature: Flash messages
  In order to display messages on redirects
  As a developer
  I need to set session messages that will display once

  Rules:
    - Messages are stores in the session
    - A call to get all messages will flush them from session
    - There wil be 4 types:
      - success
      - info
      - warning
      - danger (error)

  Scenario: Display flash messages after redirect
    Given I am on "messages/set-messages"
    Then I should be on "messages/show"
    And I should see "Test!" in the ".alert-danger" element
    And I should see "Test!" in the ".alert-info" element
    And I should see "Test!" in the ".alert-warning" element
    And I should see "Test!" in the ".alert-success" element