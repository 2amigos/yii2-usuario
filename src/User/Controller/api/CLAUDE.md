# API controllers

Active REST API controllers live here (the `rest/` sibling folder is deprecated).

## Authorization model

- `AdminController` — every action calls `checkAccess()`, which requires
  `module->enableRestApi` and `Yii::$app->user->identity->isAdmin`. Keep this
  guard on any new action.
- `AuthController` — RBAC introspection endpoints (`roles`, `permissions`) must
  scope their output by privilege: **admins** (`identity->isAdmin`) get the full
  RBAC set (`authManager->getRoles()` / `getPermissions()`); **any other user**
  gets only what is assigned to them (`getRolesByCurrentUser()` /
  `getPermissionsByUser($userId)`). Never return the whole RBAC map to a
  non-admin — it leaks the authorization schema.
- Authentication is via `JwtHttpBearerAuth` / `module->authenticatorClass`.
  Authentication alone is NOT authorization — add an explicit privilege check.
