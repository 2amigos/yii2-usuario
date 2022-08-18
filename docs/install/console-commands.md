Console Commands
================

The module comes with a set of console commands to facilitate some of the  most common actions during development time: 

- `user/create` to create a new user
- `user/confirm` to confirm a user
- `user/delete` to delete a user 
- `user/password` to update a user's password


Configuration
-------------

To enable the commands add the following configuration details to your console config of your application: 

```php
// ... 

'modules' => [
    'user' =>  Da\User\Module::class,
]
```

How to Use Them
---------------

#### user/create

If password is not set, it will automatically generate it. The newly created user will receive an email message with its 
new credentials. If a role is provided, it will assign it to the user. Is important not note, that if the role doesn't 
exist, the command will create it.

```php 
./yii user/create <email> <username> [password] [role]
```

#### user/confirm 

You can confirm a user whether by using its email or username.

```php 
./yii user/confirm <email|username>
```

#### user/delete 

You can delete a user whether by using its email or username.

```php 
./yii user/delete <email|username>
```

#### user/password 

You can update a user's password whether by using its email or username.

```php 
./yii user/password <email|username> <password>
```


© [2amigos](http://www.2amigos.us/) 2013-2019
