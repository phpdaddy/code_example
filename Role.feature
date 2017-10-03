Feature: Test role REST controller

  Scenario: Creating a new role
    When I send a POST request to "/roles" with values:
      | name | NEW_ACTION |

    And I set header "Accept-Charset" with value "utf-8"
    Then response code should be 201
    And response should exactly contain json:
            """"
            {
                "id": 29,
                "name": "NEW_ACTION"
            }
            """

  Scenario: Finding an existing role
    When I send a GET request to "/roles/1"
    Then response code should be 200
    And response should exactly contain json:
            """"
            {
                "id": 1,
                "name": "ROLE_VIEW"
            }
            """

  Scenario: Updating an existing role
    When I send a PUT request to "/roles/1" with values:
      | name | ROLE_VIEW_MODIFIED |
    Then response code should be 200
    And response should exactly contain json:
            """"
            {
                "id": 1,
                "name": "ROLE_VIEW_MODIFIED"
            }
            """

  Scenario: Deleting existing roles
    When I send a DELETE request to "/roles/1"
    Then response code should be 204

    When I send a GET request to "/roles/1"
    Then response code should be 404
    And response should contain "Not found"

  Scenario: Deleting non-existent roles
    When I send a DELETE request to "/roles/0"
    Then response code should be 404
    And response should contain "Not found"