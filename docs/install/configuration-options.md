Configuration Options
=====================

The module comes with a set of attributes to configure. The following is the list of all available options:

#### enableSessionHistory (Type: `boolean, integer`, Default value: `false`)

If this option is to `true`, session history will be kept, [more](../guides/how-to-use-session-history.md).

#### numberSessionHistory (Type: `boolean, integer`, Default value: `false`)

Number of expired storing records `session history`, values:

- `false` Store all records without deleting
- `integer` Count of records for storing

#### timeoutSessionHistory (Type: `boolean, integer`, Default value: `false`)

How long store `session history` after expiring, values:

- `false` Store all records without deleting
- `integer` Time for storing after expiring in seconds

#### enableTwoFactorAuthentication (type: `boolean`, default: `false`)

Setting this attribute will allow users to configure their login process with two-factor authentication.

#### twoFactorAuthenticationCycles (type: `integer`, default: `1`)

By default, Google Authenticator App for two-factor authentication cycles in periods of 30 seconds. In order to allow
a bigger period so to avoid out of sync issues.

#### twoFactorAuthenticationValidators (type: `array`)

An array of arrays of channels availables for two factor authentication. The keys in the arrays have the following meaning:
class: it will be the validator class with namespace;
name: the name that will be displayed in the section to the user;
configurationUrl: the url to the action that will dispaly the configuration form for the validator;
codeDurationTime: time duration of the code in session in seconds (not applicable for Google authenticator);
smsSender: the reference to SmsSenderInterface for managing SMS send;
enabled: true if you want to enable the channel, false otherwise.

The following is the default configuration:

```php
'google-authenticator'=>[
    'class'=>\Da\User\Validator\TwoFactorCodeValidator::class,
    'description'=>Yii::t('usuario', 'Google Authenticator'),
    'configurationUrl'=>'user/settings/two-factor',
    'enabled'=>true
],
'email'=>[
    'class'=>\Da\User\Validator\TwoFactorEmailValidator::class,
    'description'=>Yii::t('usuario', 'Email'),
    'configurationUrl'=>'user/settings/two-factor-email',
    'codeDurationTime'=>300,
    'enabled'=>true
],
'sms'=>[
    'class'=>\Da\User\Validator\TwoFactorTextMessageValidator::class,
    'description'=>Yii::t('usuario', 'Text message'),
    'configurationUrl'=>'user/settings/two-factor-sms',
    'codeDurationTime'=>300,
    'smsSender'=>'smsSender',
    'enabled'=>true
],
```

For instructions about implementation of SMS sending see at the following link: <https://www.yiiframework.com/extension/yetopen/yii2-sms-aruba>

#### twoFactorAuthenticationForcedPermissions (type: `array`, default: `[]`)

The list of permissions for which two factor authentication is mandatory. In order to perform the check in every action,
you must configure a filter into your config file like this:

```php
use Da\User\Filter\TwoFactorAuthenticationEnforceFilter;
...
'on beforeAction' => function() {
        Yii::$app->controller->attachBehavior(
            'enforceTwoFactorAuthentication',[
                'class' => TwoFactorAuthenticationEnforceFilter::class,
                'except' => ['login', 'logout', 'account', 'two-factor', 'two-factor-enable'],
            ]
        );
    },
...
```

This will redirect the user to their account page until the two factor authentication is enabled.
Otherwise you can set the filter on each controller you need.

#### enableGdprCompliance (type: `boolean`, default: `false`)

Setting this attribute enables a serie of measures to comply with EU GDPR regulation, like data consent, right to be forgotten and data portability.

#### gdprPrivacyPolicyUrl (type: `array`, default: null)

The link to privacy policy. This will be used on registration form as "read our pivacy policy". It must follow the same format as `yii\helpers\Url::to`

#### gdprExportProperties (type: `array`)

An array with the name of the user identity properties to be included when user request download of his data.
Names can include relations like `profile.name`.

Defaults to:

```php
      [
        'email',
        'username',
        'profile.public_email',
        'profile.name',
        'profile.gravatar_email',
        'profile.location',
        'profile.website',
        'profile.bio'
      ]
```

#### gdprAnonymizePrefix (type: `string`, default: `GDPR`)

Prefix to be used as a replacement when user requeste deletion of his data

#### gdprConsentMessage (type: `string`)

Use this to customize the message that will appear as hint in the give consent checkbox.
If you leave it empty the next message will be used:

>I agree processing of my personal data and the use of cookies to facilitate the operation of this site. For more information read our privacy policy

#### GdprRequireConsentToAll (type `boolean`, default `false`)

Whether require to already registered user give consent to process their data. According to GDPR this is mandatory.
To forbid user access to any function, until it gives consent, use the AccessRuleFilter included with this module.

#### GdprConsentExcludedUrls (type `array`, default `['user/settings/*']`)

List of urls that does not require explicit data processing consent to be accessed, like own profile, account... You can use wildcards like `route/to/*` .

#### enableRegistration (type: `boolean`, default: `true`)

Setting this attribute allows the registration process. If you set it to `false`, the module won't allow users to
register by throwing a `NotFoundHttpException` if the `RegistrationController::actionRegister()` is accessed.

#### enableEmailConfirmation (type: `boolean`, default: `true`)

If `true`, the module will send an email with a confirmation link that user needs to click through to complete its
registration process.

#### enableFlashMessages (type: `boolean`, default: `true`)

If `true` views will display flash messages. Disable this if you want to handle messages display in your views.

#### enableSwitchIdentities (type: `boolean`, default: `true`)

If `true` allows switching identities for the admin user.

#### generatePasswords (type: `boolean`, default: `true`)

If `true` the password field will be hidden on the registration page and passwords will be generated automatically and
sent to the user via email.

#### allowUnconfirmedEmailLogin (type: `boolean`, default: `false`)

If `true` it will allow users to login with unconfirmed emails.
  
#### allowPasswordRecovery (type: `boolean`, default: `true`)

If `true` it will enable password recovery process.

#### allowAdminPasswordRecovery (type: `boolean`, default: `true`)

If `true` it will enable administrator to send a password recovery email to a user.

#### maxPasswordAge (type: `integer`, default: `null`)

If set to an integer value it will check user password age. If the days since last password change are greater than this configuration value
user will be forced to change it. This enforcement is done only at login stage. In order to perform the check in every action you must configure
a filter into your controller like this:

```
use Da\User\Filter\PasswordAgeEnforceFilter;
class SiteController extends Controller
{
    public function behaviors()
    {
        return [
            [...]
            'enforcePasswordAge' => [
                'class' => PasswordAgeEnforceFilter::className(),
            ],
```

This will redirect the user to their account page until the password has been updated.

#### allowAccountDelete (type: `boolean`, default: `false`)

If `true` users will be able to remove their own accounts.

#### emailChangeStrategy (type: `integer`, default: `MailChangeStrategyInterface::TYPE_DEFAULT`)

Configures one of the three ways available to change user's password:

- **MailChangeStrategyInterface::TYPE_DEFAULT**: A confirmation message will be sent to the new user's email with a link
    that needs to be click through to confirm  it.
- **MailChangeStrategyInterface::TYPE_INSECURE**: Email will be changed without any confirmation message.
- **MailChangeStrategyInterface::TYPE_SECURE**: A confirmation message will be sent to the previous and new user's email
    with a link that would require both to be click through to confirm the change.

#### rememberLoginLifespan (type: `integer`, default: `1209600`)

Configures the time length in seconds a user will be remembered without the need to login again. The default time is 2
weeks.

#### tokenConfirmationLifespan (type: `integer`, default: `86400`)

Configures the time length in seconds a confirmation token is valid. The default time is 24 hours.
  
#### tokenRecoveryLifespan (type: `integer`, default: `21600`)

Configures the time length in seconds a recovery token is valid. The default time is 6 hours.

#### administrators (type: `array`, default: `[]`)

Configures the usernames of those users who are considered `admininistrators`. The administrators can be
configured here or throughout RBAC with a special permission name. The recommended way is throughout
`administratorPermissionName` as they can be set dynamically throughout the RBAC interface, but use this attribute for
simple backends with static administrators that won't change throughout time.

#### administratorPermissionName (type: `string`, default: `null`)

Configures the permission name for `administrators`. See [AuthHelper](../../src/User/Helper/AuthHelper.php).

#### prefix (type: `string`, default: `user`)

Configures the URL prefix for the module.

#### mailParams (type: `array`, default: `[]`)

Configures the parameter values used on [MailFactory](../../src/User/Factory/MailFactory.php). The default values are:

```php
[
    'fromEmail' => 'no-reply@example.com',
    'welcomeMailSubject' => Yii::t('usuario', 'Welcome to {0}', $app->name),
    'confirmationMailSubject' => Yii::t('usuario', 'Confirm account on {0}', $app->name),
    'reconfirmationMailSubject' => Yii::t('usuario', 'Confirm email change on {0}', $app->name),
    'recoveryMailSubject' => Yii::t('usuario', 'Complete password reset on {0}', $app->name),
    'twoFactorMailSubject' => Yii::t('usuario', 'Code for two factor authentication on {0}', $app->name),
]
```

#### blowfishCost (type: `integer`, default: `10`)

Is the cost parameter used by the Blowfish hash algorithm. The higher the value of cost, the longer it takes to generate
the hash and to verify a password against it. Higher cost therefore slows down a brute-force attack. For the best
protected against brute-force attacks, set it to the highest value that is tolerable on production servers. The time
taken to compute the hash doubles for every increment by one of `$blowfishCost`.

#### consoleControllerNamespace (type: `string`, default: `Da\User\Command`)

Allows customization of the console application controller namespace for the module.

#### controllerNamespace (type: `string`, default: `Da\User\Controller`)

Allows customization of the web application controller namespace for the module.

#### classMap (type: `array`, default: `[]`)

Configures the definitions of the classes as they have to be override. For more information see
[Overriding Classes](../customizing/overriding-classes.md).

#### routes (type: `array`, default: `[]` )

The routes (url rules) of the module for the URL management. The default values are:

```php
[
    '<id:\d+>' => 'profile/show',
    '<action:(login|logout)>' => 'security/<action>',
    '<action:(register|resend)>' => 'registration/<action>',
    'confirm/<id:\d+>/<code:[A-Za-z0-9_-]+>' => 'registration/confirm',
    'forgot' => 'recovery/request',
    'recover/<id:\d+>/<code:[A-Za-z0-9_-]+>' => 'recovery/reset',
    'settings/<action:\w+>' => 'settings/<action>',
]
```

#### viewPath (type: `string`, default: `@Da/User/resources/views`)

Configures the root directory of the view files. See [overriding views](../customizing/overriding-views.md).

#### switchIdentitySessionKey (type: `string`, default: `yuik_usuario`)

Configures the name of the session key that will be used to hold the original admin identifier.

#### restrictUserPermissionAssignment (type: `boolean`, default: `false`)

If `false`, allow the assignment of both roles and permissions to users.
Set to `true` to restrict user assignments to roles only.

#### disableIpLogging (type: `boolean`, default: `false`)

If `true` registration and last login IPs are not logged into users table, instead a dummy 127.0.0.1 is used

#### minPasswordRequirements (type: `array`, default: `['lower' => 1, 'digit' => 1, 'upper' => 1]`)

Minimum requirements when a new password is automatically generated.
Array structure: `"requirement" => minimum_number_characters`.

Possible array keys:

- lower: minimum number of lowercase characters;
- upper: minimum number of uppercase characters;
- digit: minimum number of digits;
- special: minimum number of special characters;
- min: minimum number of characters (= minimum length).

#### enableRestApi (type: `boolean`, default: `false`)

Whether to enable REST APIs.

#### authenticatorClass (type: `string`, default: `yii\filters\auth\QueryParamAuth`)

Which class to use as authenticator for REST API.
Possible values ([official documentation](https://www.yiiframework.com/doc/guide/2.0/en/rest-authentication)):
- `HttpBasicAuth`
- `HttpBearerAuth`
- `QueryParamAuth`.

Default value = `yii\filters\auth\QueryParamAuth` class, therefore access tokens are sent as query parameter; for instance: `https://example.com/users?access-token=xxxxxxxx`.

#### adminRestPrefix (type: `string`, default: `user/api/v1`)

Prefix for the pattern part of every rule for REST admin controller.

#### adminRestRoutePrefix (type: `string`, default: `user/api/v1`)

Prefix for the route part of every rule for REST admin controller.

#### adminRestRoutes (type `array`)

Routes for REST admin controller.

Default value: 
```php
[
    'GET,HEAD users' => 'admin/index',
    'POST users' => 'admin/create',
    'PUT,PATCH users/<id>' => 'admin/update',
    'GET,HEAD users/<id>' => 'admin/view',
    'DELETE users/<id>' => 'admin/delete',
    'users/<action>/<id>' => 'admin/<action>',
    'users/<id>' => 'admin/options',
    'users' => 'admin/options',
];
```


© [2amigos](http://www.2amigos.us/) 2013-2019
