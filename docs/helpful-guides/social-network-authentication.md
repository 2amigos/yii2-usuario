Social Network Authentication
=============================

If you wish to add user registration and login throughout social networks the first thing you need to do is to add the 
official [Yii's auth client extension](https://github.com/yiisoft/yii2-authclient) to your application. The preferred 
way to install is through [composer](http://getcomposer.org/download/).

Either run

```
composer require --prefer-dist yiisoft/yii2-authclient
```

or add

```json
"yiisoft/yii2-authclient": "~2.1.0"
```

to the `require` section of your composer.json.

After you need to configure the `authClientCollection::clients` on your Application `components` section: 

```php 
// ... 
'components' => [
    // ...
    'authClientCollection' => [
        'class' => 'yii\authclient\Collection',
        'clients' => [
            'facebook' => [
                'class' => 'Da\User\AuthClient\Facebook',
                'clientId' => 'facebook_client_id',
                'clientSecret' => 'facebook_client_secret'
            ]
        ]
    ]
]
```

We have override the clients that come with Yii official's auth extension so to provide them with a signature that 
would help us access the email and username with ease. 

The following is the list of clients supported by the module: 

- **Facebook** - `Da\User\AuthClient\Facebook`
- **Github** - `Da\User\AuthClient\Github`
- **Google** - `Da\User\AuthClient\Google`
- **LinkedIn** - `Da\User\AuthClient\LinkedIn`
- **Twitter** - `Da\User\AuthClient\Twitter`
- **VKontakte** - `Da\User\AuthClient\VKontakte`
- **Yandex** - `Da\User\AuthClient\Yandex`

For further information about how to configure the clients, please visit the 
[Official Yii Auth Client Extension documentation](https://github.com/yiisoft/yii2-authclient/blob/master/docs/guide/installation.md).


© [2amigos](http://www.2amigos.us/) 2013-2019
