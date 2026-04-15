## ADDED Requirements

### Requirement: Get Access Token
The system SHALL be able to retrieve an access token from Olsera using the `app_id` and `secret_key` with the `secret_key` grant type.

#### Scenario: Successful Token Retrieval
- **WHEN** the system sends a POST request to the token endpoint with valid `app_id`, `secret_key`, and `grant_type="secret_key"`
- **THEN** the system receives an access token and refresh token, and logs the raw response to `storage/olsera/`

### Requirement: Refresh Access Token
The system SHALL be able to refresh an expired access token using a valid `refresh_token` with the `refresh_token` grant type.

#### Scenario: Successful Token Refresh
- **WHEN** the system sends a POST request to the token endpoint with a valid `refresh_token` and `grant_type="refresh_token"`
- **THEN** the system receives a new access token and refresh token, and logs the raw response to `storage/olsera/`
