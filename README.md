Migrations
==========

Add the following settings to your console application configuration file:

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

This will allow you to run only a single console command:

```
./yii migrate
```

It prevents you from manual tracking of new migrations coming from our extension. What if we would add a new migration class, and you forgot to run appropriate console command to launch them?

Without namespaced migrations it would be:

```
./yii migrate
./yii migrate --migrationPath="@Da/User/resources/migrations"
```

Without namespaced migrations it's just:

```
./yii migrate
```
