# REST controllers (deprecated)

All controllers under `Da\User\Controller\rest` are **deprecated**.

- Do not add new endpoints or features here.
- New API work goes under `Da\User\Controller\api` (e.g. `api/v1/AuthController`).
- Existing REST controllers are kept only for backward compatibility; each class
  carries an `@deprecated` docblock tag.

`SecurityController` here intentionally does **not** include the JWT-on-login
behaviour — that lives in the `api` controllers. Keep this controller at its
plain login/password + JWT-bearer-auth baseline.
