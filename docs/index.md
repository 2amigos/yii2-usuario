# Yii2 Usuario Extension

[![Build Status](https://travis-ci.org/2amigos/yii2-usuario.svg?branch=master)](https://travis-ci.org/2amigos/yii2-usuario)
[![Documentation Status](https://readthedocs.org/projects/yii2-usuario/badge/?version=latest)](http://yii2-usuario.readthedocs.io/en/latest/?badge=latest)
[![Join the chat at https://gitter.im/2amigos/yii2-usuario](https://badges.gitter.im/2amigos/yii2-usuario.svg)](https://gitter.im/2amigos/yii2-usuario?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge)  
[![Latest Stable Version](https://poser.pugx.org/2amigos/yii2-usuario/version)](https://packagist.org/packages/2amigos/yii2-usuario)
[![Total Downloads](https://poser.pugx.org/2amigos/yii2-usuario/downloads)](https://packagist.org/packages/2amigos/yii2-usuario)
[![Latest Unstable Version](https://poser.pugx.org/2amigos/yii2-usuario/v/unstable)](//packagist.org/packages/2amigos/yii2-usuario)  
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/2amigos/yii2-usuario/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/2amigos/yii2-usuario/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/2amigos/yii2-usuario/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/2amigos/yii2-usuario/?branch=master)

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

Before starting to work with database, please ensure you have deleted `m130524_201442_init.php` migration file
which comes from the default installation of the Advanced Application Template. It's located at
`%PROJECT_DIR%/console/migrations/m130524_201442_init.php` path.

There are two ways to apply migrations of this extension, the first one:

```php
./yii migrate --migrationPath="@Da/User/Migration"
./yii migrate
```

First command applies migration set of the user module, and the second one is for application migrations.

> Note, you cannot mix two ways: choose one of them, and stick with it.

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

> Namespaced migrations were introduced in Yii 2.0.10, so before using them consider updating your framework
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

Enhancing and Overriding
------------------------

- [How to Enhance a User Model](enhancing-and-overriding/how-to-enhance-a-user-model.md)

Helpful Guides
--------------

- [Separate Frontend and Backend Sessions](helpful-guides/separate-frontend-and-backend-sessions.md)

Contributing
------------

- [How to Contribute](contributing/how-to.md)
- [Clean Code](contributing/clean-code.md)

© [2amigos](http://www.2amigos.us/) 2013-2017
