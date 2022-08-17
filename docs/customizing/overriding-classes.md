Overriding Classes
==================

This module makes extensive use of the Yii2's Dependency Injection Container. The module has a special attribute 
named `classMap` where it allows you to override specific classes. 

The following are the classes that you can override throughout that attribute: 

- Model Classes (AR)
    - User 
    - SocialNetworkAccount
    - Profile
    - Token
    - Assignment
    - Permission
    - Role
- Search Classes 
    - UserSearch
    - PermissionSearch
    - RoleSearch
- Form Classes
    - RegistrationForm
    - ResendForm
    - LoginForm
    - SettingsForm
    - RecoveryForm
- Service Classes
    - MailService
    

How to Override
---------------

The `classMap` contains an easy to recognize name and their correspondent definition. The default configuration can be 
seen at the `Bootstrap` class:

```php
$defaults = [
    // --- models
    'User' => 'Da\User\Model\User',
    'SocialNetworkAccount' => 'Da\User\Model\SocialNetworkAccount',
    'Profile' => 'Da\User\Model\Profile',
    'Token' => 'Da\User\Model\Token',
    'Assignment' => 'Da\User\Model\Assignment',
    'Permission' => 'Da\User\Model\Permission',
    'Role' => 'Da\User\Model\Role',
    // --- search
    'UserSearch' => 'Da\User\Search\UserSearch',
    'PermissionSearch' => 'Da\User\Search\PermissionSearch',
    'RoleSearch' => 'Da\User\Search\RoleSearch',
    // --- forms
    'RegistrationForm' => 'Da\User\Form\RegistrationForm',
    'ResendForm' => 'Da\User\Form\ResendForm',
    'LoginForm' => 'Da\User\Form\LoginForm',
    'SettingsForm' => 'Da\User\Form\SettingsForm',
    'RecoveryForm' => 'Da\User\Form\RecoveryForm',
    // --- services
    'MailService' => 'Da\User\Service\MailService',
];
```

As you can see, the only thing we need to do is actually modify its definition. For example, the following configuration 
will override the `RegistrationForm` class:

```php 
namespace app\forms;

use Da\User\Form\RegistrationForm as BaseForm;


class RegistrationForm extends BaseForm {

    /**
     * Override from parent
     */
    public function rules() {
        // your logic
    }
}

```
Now, to tell the module to use your class instead, you simply need to update the definition of that class into the 
the `Module::classMap` attribute.

```php

// ...
 
'modules' => [
    'user' => [
        'class' => Da\User\Module::class,
        'classMap' => [
            'RegistrationForm' => 'app\forms\RegistrationForm'
        ]
    ]
]

```

The definition can be any of the following (from Yii2's DI container): 

- a **PHP callable**: The callable will be executed when `Container::get()]]` is invoked. The signature of the callable
  should be `function ($container, $params, $config)`, where `$params` stands for the list of constructor
  parameters, `$config` the object configuration, and `$container` the container object. The return value
  of the callable will be returned by `Container::get()]]` as the object instance requested.
- a **configuration array**: the array contains name-value pairs that will be used to initialize the property
  values of the newly created object when `Container::get()]]` is called. The `class` element stands for the
  the class of the object to be created. If `class` is not specified, `$class` will be used as the class name.
- a **string**: a class name, an interface name or an alias name.

> See [how to enhance a User model](how-to-enhance-a-user-model.md) guide to see a practical example.

© [2amigos](http://www.2amigos.us/) 2013-2019
