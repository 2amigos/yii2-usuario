Available Actions
=================

The following is the list of action provided by the module: 

- **/user/registration/register** Displays registration form
- **/user/registration/resend**   Displays resend form
- **/user/registration/confirm**  Confirms a user (requires *id* and *token* query params)
- **/user/security/login**        Displays login form
- **/user/security/logout**       Logs the user out (available only via POST method)
- **/user/recovery/request**      Displays recovery request form
- **/user/recovery/reset**        Displays password reset form (requires *id* and *token* query params)
- **/user/settings/profile**      Displays profile settings form
- **/user/settings/account**      Displays account settings form (email, username, password)
- **/user/settings/networks**     Displays social network accounts settings page
- **/user/profile/show**          Displays user's profile (requires *id* query param)
- **/user/admin/index**           Displays user management interface
- **/user/admin/create**          Displays create user form
- **/user/admin/update**          Displays update user form (requires *id* query param)
- **/user/admin/update-profile**  Displays update user's profile form (requires *id* query param)
- **/user/admin/info**            Displays user info (requires *id* query param)
- **/user/admin/assignments**     Displays rbac user assignments (requires *id* query param)
- **/user/admin/confirm**         Confirms a user (requires *id* query param)
- **/user/admin/delete**          Deletes a user (requires *id* query param)
- **/user/admin/block**           Blocks a user (requires *id* query param)
- **/user/admin/switch-identity** Switch identities between the current admin and user on list
- **/user/role/index**            Displays rbac roles management interface
- **/user/role/create**           Displays create rbac role form
- **/user/role/update**           Displays update rbac role form (requires *name* query param)
- **/user/role/delete**           Deletes a rbac role (requires *name* query param)
- **/user/permission/index**      Displays rbac permissions management interface
- **/user/permission/create**     Displays create rbac permission form
- **/user/permission/update**     Displays update rbac permission form (requires *name* query param)
- **/user/permission/delete**     Deletes a rbac permission (requires *name* query param)
- **/user/rule/index**            Displays rbac permissions management interface
- **/user/rule/create**           Displays create rbac rule form
- **/user/rule/update**           Displays update rbac rule form (requires *name* query param)
- **/user/rule/delete**           Deletes a rbac rule (requires *name* query param)

The module overrides some to make it simpler:  

```php 
'<id:\d+>' => 'profile/show',
'<action:(login|logout)>' => 'security/<action>',
'<action:(register|resend)>' => 'registration/<action>',
'confirm/<id:\d+>/<code:[A-Za-z0-9_-]+>' => 'registration/confirm',
'forgot' => 'recovery/request',
'recover/<id:\d+>/<code:[A-Za-z0-9_-]+>' => 'recovery/reset'
```

So they become:
 
- **/user/{id}**                   Displays user's profile (requires *id* query param)
- **/user/login**                  Displays login form
- **/user/logout**                 Logs out a user
- **/user/register**               Displays registration form
- **/user/resend**                 Displays resend form
- **/user/confirm/{id}/{token}**   Confirms a user (requires *id* and *token* query params)
- **/user/forgot**                 Displays recovery request form
- **/user/recover/{id}/{token}**   Displays password reset form (requires *id* and *token* query params)


You can override them by setting the module's routes to an empty array. Then, configure the routes as you please.


© [2amigos](http://www.2amigos.us/) 2013-2017
