# REST API Guide

This guide covers the new REST API endpoints introduced in yii2-usuario, starting with the login functionality.

## Security Controller

A new REST controller has been added under the namespace `Da\User\Controller\api\v1\SecurityController`. It handles authentication-related actions via API.

### Login Endpoint

The login endpoint allows users to authenticate via REST and receive an access token.

- **Endpoint**: POST `/user/api/v1/security/login`.
- **Request Body Parameters**:
    - `login`: The username or email.
    - `password`: The user's password.
- **Behavior**:
    - Validates credentials using the `LoginForm`.
    - On success: Logs the user in, updates `last_login_at` and `last_login_ip` (IP logging can be disabled via module config), triggers relevant events (`EVENT_BEFORE_LOGIN` and `EVENT_AFTER_LOGIN`), and returns a JSON response with the access token.
    - On failure: Triggers `EVENT_FAILED_LOGIN` and throws an `UnauthorizedHttpException`.
- **Response (Success):**
```json
{
    "token": "your_access_token_here"
}
```
The token is retrieved via the User's `getAccessToken()` method (see below for implementation details).
- **Notes:**
    - Two-factor authentication (2FA) support is planned but not yet implemented (see TODO in the code).
    - Authentication behaviors: Uses `CompositeAuth` for other actions, but `login` is exempt.
    - Ensure your application routes are configured to handle the /`user/api/v1/security/*` path.

## User Model Updates

The `Da\User\Model\User` model now includes a new method for access token handling, primarily for API integrations.

### getAccessToken()
- **Signature**: `public function getAccessToken()`
- **Description**: Retrieves the user's access token. By default, this method throws a `NotSupportedException` to encourage implementation in a subclass or extension of the User model.
- **Implementation Example**: Override this method in your application's User model (e.g., if extending `Da\User\Model\User`):
```php
public function getAccessToken()
{
    // Implement your token logic here, e.g., generate or retrieve a JWT or API key
    return 'implemented_token';
}
```
- **Usage**: Called after successful login in the REST API to include the token in the response. Customize it to fit your authentication system (e.g., integrating with JWT or OAuth).

## Configuration Notes
To use the REST API:
- Ensure the module is configured in your `config.php` as usual: `'modules' => ['user' => ['class' => Da\User\Module::class]]`.
- No additional configuration is required for the basic login endpoint, but you may need to set up CORS or API-specific auth filters in your application.
- Events: The login process triggers standard FormEvent events, allowing custom logic before/after login.

For more on events or extending the module, refer to the Events section.

This feature enhances yii2-usuario for API-driven applications. Contributions or issues can be reported on GitHub.