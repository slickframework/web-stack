Feature: The catch all route
  In order to access a controller handler action without a defined route
  As a developer
  I need a catch-all route that will map the URI to the controller/method handler

  Rules:
    - The first part in the URI is the controller name:
      users/edit/1 -> controller will be Users
    - The second part if exists will map to the method on the controller:
      users/view -> Users::view() will be called
    - The remaining parts will be passed ti the called method as arguments:
      users/view/32/small -> Users::view(23, 'small')
    - Underscored names will be camelCased:
      order_statuses -> controller will be OrderStatus
    - Dashed names will be camelCased
      users/update-status/43 -> Users::updateStatus(43) will be called

  Scenario: call controller with argument
    Given I am on "catch-all/show_state/23"
    Then I should see "Argument was: 23"