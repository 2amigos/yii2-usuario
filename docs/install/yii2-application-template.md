2amigos Application Template
============================

This Application Template is our proposed structure for your Yii2 applications. It makes use 
of a special library named `ConfigKit`

> For further information regarding the use of this template, please visit its 
[README file](https://github.com/2amigos/yii2-app-template).

Step 1 - Install The Application template
-----------------------------------------

We will assume that you have composer installed globally on your computer and also the 
`fxp/composer-asset/plugin:^1.3` that is required for all Yii2 apps.

```bash
composer create-project --prefer-dist --stability=dev 2amigos/yii2-app-template your-site-name
```

Step 2 - Configure your application
-----------------------------------

Go to the `config/web/modules` folder and create a new PHP file named `user.php`. Then on in its 
contents write the configuration for the module: 


```php
<?php 

return [
    'class' => Da\User\Module::class
];
```

Step 3 - Apply database schema migrations
-----------------------------------------

This is obvious extension like our which deals with users, roles, permissions, etc. have to use some database.
Our migrations are namespaced and available in `Da\User\Migration` namespace.

Before starting to work with database, please ensure you have deleted `m130524_201442_init.php` migration file
which comes from the default installation of the Advanced Application Template. It's located at
`%PROJECT_DIR%/console/migrations/m130524_201442_init.php` path.

There are two ways to apply migrations of this extension, the first one:

```php
./yii migrate --migrationNamespaces=Da\\User\\Migration
./yii migrate --migrationPath=@yii/rbac/migrations
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
            'migrationPath' => [
                '@app/migrations',
                '@yii/rbac/migrations', 
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

© [2amigos](http://www.2amigos.us/) 2013-2019


