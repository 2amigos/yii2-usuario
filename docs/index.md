# Yii2 Usuario Extension

[![Documentation Status](https://readthedocs.org/projects/yii2-usuario/badge/?version=latest)](http://yii2-usuario.readthedocs.io/en/latest/?badge=latest)
[![Join the chat at https://gitter.im/2amigos/yii2-usuario](https://badges.gitter.im/2amigos/yii2-usuario.svg)](https://gitter.im/2amigos/yii2-usuario?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge)
[![Packagist Version](https://img.shields.io/packagist/v/2amigos/yii2-usuario.svg?style=flat-square)](https://packagist.org/packages/2amigos/yii2-usuario)
[![Latest Stable Version](https://poser.pugx.org/2amigos/yii2-usuario/version)](https://packagist.org/packages/2amigos/yii2-usuario)
[![Total Downloads](https://poser.pugx.org/2amigos/yii2-usuario/downloads)](https://packagist.org/packages/2amigos/yii2-usuario)
[![Latest Unstable Version](https://poser.pugx.org/2amigos/yii2-usuario/v/unstable)](//packagist.org/packages/2amigos/yii2-usuario)  
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/2amigos/yii2-usuario/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/2amigos/yii2-usuario/?branch=master)

Yii 2 usuario is a highly customizable and extensible user management, RBAC management, authentication, 
and authorization Yii2 module extension. 

It works extensively with Yii's Container making it really easy to override absolutely anything within its core. The 
module is built to work out of the box with some minor config tweaks and it comes with the following features: 
 
- Backend user/profile/account management
- Backend RBAC management 
- Login via username/email + password process
- Login via social network process
- Password recovery process


## Getting Started

This extension has been built to be working `out of the box`, that is, after you install its migrations and configure 
the module on your application structure, you should be set to go. 

#### Step 1 - Download

You can download it and place it on your third party libraries folder but we highly recommend that you install it 
through [composer](http://getcomposer.org/download/).

Either run

```bash
$ composer require 2amigos/yii2-usuario:~1.0
```

or add

```
"2amigos/yii2-usuario": "~1.0"
```

to the `require` section of your `composer.json` file.

#### Step 2 - Apply database schema migrations

This is obvious extension like our which deals with users, roles, permissions, etc. have to use some database.
Our migrations are namespaced and available in `Da\User\Migration` namespace.

> **Note**: If you are using Yii2's Advanced Application Template, before starting to work with database, please ensure 
you have deleted `m130524_201442_init.php` migration file which comes from the default installation. It's located at
`%PROJECT_DIR%/console/migrations/m130524_201442_init.php` path.

There are two ways to apply migrations of this extension, the first one:

```php
./yii migrate --migrationPath=@Da/User/Migration
./yii migrate --migrationPath=@yii/rbac/migrations
./yii migrate
```

First command applies migration set of the user module, and the second one is for Yii RBAC migration, the third is for 
your own application migrations.

> **Note**: you cannot mix two ways: choose one of them, and stick with it.

The second way is more comfortable, and you don't have to remember to launch first command every time you obtain
new version of our extension. First of all add the following lines to the file
`%PROJECT_DIR%/console/config/main.php`:

```php
return [
    // ...
    'controllerMap' => [
        'migrate' => [
            'class' => \yii\console\controllers\MigrateController::class,
            'migrationNamespaces' => [
                'Da\User\Migration',
            ],
        ],
    ],
    // ...
];
```

This basically instructs your application to always try to use migrations from the given namespace. Which again
is very convenient way to track new migration classes coming from this and possibly other extensions and sources. 

> **Note**: You will still have to apply Yii 2 RBAC migrations by executing 
> `./yii migrate --migrationPath=@yii/rbac/migrations`. 
> Also, namespaced migrations were introduced in Yii 2.0.10, so before using them consider updating your framework
> installation version.

#### Step 3 - Configure

Once we have it installed, we have to configure it on your `config.php` file. 


```php 
'modules' => [
    'user' => [
        'class' => Da\User\Module::class,
    ]
]
```

Configuration may differ from template to template, the following are some guidelines for sidekit app template and 
the official Yii2 advanced application template: 

- [Sidekit Application Template](installation/sidekit-application-template.md)
- [Advanced Application Template](installation/advanced-application-template.md)

See also all the possible configuration options available: 

- [Configuration Options](installation/configuration-options.md)

Enhancing and Overriding
------------------------

- [How to Enhance a User Model](enhancing-and-overriding/how-to-enhance-a-user-model.md)
- [Overriding Classes](enhancing-and-overriding/overriding-classes.md) 
- [Overriding Views](enhancing-and-overriding/overriding-views.md)

Helpful Guides
--------------

- [Separate Frontend and Backend Sessions](helpful-guides/separate-frontend-and-backend-sessions.md)

Contributing
------------

- [How to Contribute](contributing/how-to.md)
- [Clean Code](contributing/clean-code.md)

© [2amigos](http://www.2amigos.us/) 2013-2017
