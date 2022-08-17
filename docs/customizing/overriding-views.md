Overriding Views
================

In case you need to override the default views (that you surely have to do if you use a different them than default's 
Bootstrap), Yii2 provides a mechanism that is really easy to do: 
 
```php

// ... other configuration here

'components' => [
    'view' => [
        'theme' => [
            'pathMap' => [
                '@Da/User/resources/views' => '@app/views/user'
            ]
        ]
    ]
]

// ...
```

The above code tells Yii2 to search on `@app/view/user` for views prior to go to `@Da/User/resources/views`. That is, 
if a view is found on `@app/view/user` that matches the required render it will be displayed instead of the one on 
`@Da/User/resources/views`.

You need to remember that the folder structure on your new location must match that of the module. For example, if we 
wish to override the `login.php` view using the above setting, we would have to create the following structure on our 
path: 

```
app  [ Your root ]
|
└─ views
    └─ user
        └─ security
             login.php
```

See how it follows the same structure as within the User's module `resources/views` path? Well, that's what you should 
do with any of the others in order to override them.

There is another way, and is by setting the `viewPath` attribute of the module: 

```php
'modules' => [
    'user' => [
        'class' => Da\User\Module::class,
        'viewPath' => '@app/views/user'
    ],
],
```
But it will force you to override **ALL** views from the module. The recommended way is throughout the `theme` property 
of `view` component as previously mentioned.

© [2amigos](http://www.2amigos.us/) 2013-2019
