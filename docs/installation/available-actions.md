Available Actions
=================

The following is the list of action provided by the module: 

| Action | Description | Query params | Method available | Note  
| --- | --- | --- | --- | ---  
| **/user/registration/register** | Displays registration form
| **/user/registration/resend** | Displays resend form
| **/user/registration/connect** | Connect a social network account | *code*
| **/user/registration/confirm** | Confirms a user | *id*, *code*
| **/user/security/login** | Displays login form
| **/user/security/logout** | Logs the user out | | POST only
| **/user/security/confirm** | Social account confirm | *id*, *code* | | Query params depend of SocialNetworkAccountQuery
| **/user/security/auth** | Social account login | | | 
| **/user/recovery/request** | Displays recovery request form
| **/user/recovery/reset** | Displays password reset form | *id*, *code*
| **/user/settings/account** | Displays account settings form | | | email, username, password
| **/user/settings/confirm** | Confirms a new email | *id*, *code*
| **/user/settings/delete** | Delete self account | | POST only
| **/user/settings/disconnect** | Disconnect social account | | POST only
| **/user/settings/export** | Download personal data in a comma separated values format
| **/user/settings/gdpr-delete** | Displays delete personal data page |
| **/user/settings/networks** | Displays social network accounts settings page
| **/user/settings/privacy** | Displays GDPR data page
| **/user/settings/profile** | Displays profile settings form
| **/user/settings/two-factor** | Show 2fa (Two factor authentication) | *id* | | https://github.com/2amigos/2fa-library required
| **/user/settings/two-factor-enable** | Enabled 2fa | *id* | | https://github.com/2amigos/2fa-library required
| **/user/settings/two-factor-disable** | Disabled 2fa | *id* | POST only | https://github.com/2amigos/2fa-library required
| **/user/profile/show** | Displays user's profile | *id*
| **/user/admin/index** | Displays user management interface
| **/user/admin/create** | Displays create user form
| **/user/admin/update** | Displays update user form | *id*
| **/user/admin/update-profile** | Displays update user's profile form | *id*
| **/user/admin/info** | Displays user info | *id*
| **/user/admin/assignments** | Displays rbac user assignments | *id*
| **/user/admin/confirm** | Confirms a user | *id* | POST only
| **/user/admin/delete** | Deletes a user | *id* | POST only
| **/user/admin/block** | Blocks a user | *id* | POST only
| **/user/admin/switch-identity** | Switch identities between the current admin and user on list | | POST only
| **/user/admin/password-reset** | Send recovery message to the user | *id* | POST only
| **/user/admin/force-password-change** | Forces the user to change password at next login | *id* | POST only
| **/user/role/index** | Displays rbac roles management interface
| **/user/role/create** | Displays create rbac role form
| **/user/role/update** | Displays update rbac role form | *name*
| **/user/role/delete** | Deletes a rbac role | *name*
| **/user/permission/index** | Displays rbac permissions management interface
| **/user/permission/create** | Displays create rbac permission form
| **/user/permission/update** | Displays update rbac permission form | *name*
| **/user/permission/delete** | Deletes a rbac permission | *name*
| **/user/rule/index** | Displays rbac permissions management interface
| **/user/rule/create** | Displays create rbac rule form
| **/user/rule/update** | Displays update rbac rule form | *name*
| **/user/rule/delete** | Deletes a rbac rule | *name*

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


© [2amigos](http://www.2amigos.us/) 2013-2019
