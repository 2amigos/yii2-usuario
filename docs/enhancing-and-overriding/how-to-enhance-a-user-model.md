How to Enhance a User Model
===========================

It is obvious that if you started your project development on Yii2 Framework, then your business and domain needs
are going to be very customized, and more or less unique. While our extension (and of course Yii2 Framework itself!)
provide sensible defaults where it's possible, we encourage and keep in mind user will extend classes.

Very often you have to override and add your own domain (or application specific code) to your user model. With this
extension this is very easy and can be done in a few minutes!

For the case if you're using [Sidekit Application Template](../installation/sidekit-application-template.md) or
[Advanced Application Template](../installation/advanced-application-template.md) create the following class file
at the `%PROJECT_DIR%/common/models/User.php` path:

```php
namespace common\models;

use Da\User\Model\User as BaseUser;

class User extends BaseUser
{
}
```

Then adjust configuration of `yii2-usuario` extension module as follows:

```php
'modules' => [
    'user' => [
        'class' => Da\User\Module::class,
        'classMap' => [
            'User' => common\models\User::class,
        ],
    ],
],
```

Another way to do that is to use Yii2 Dependency Injection configuration as we extensively use service container
feature. This is good approach too:

```php
'container' => [
    'definitions' => [
        Da\User\Model\User::class => common\models\User::class,
    ],
],
```

> Please note, the method above works only starting from Yii 2.0.10 and upper. In version 2.0.9 and lower you can
> use explicit calls to DI container from application `bootstrap.php` file.

Finally you can now add new methods, properties, and other things to your new `User` model class:

```php
// model
class User extends BaseUser
{
    public function addFriend(User $friend)
    {
        // ...
    }
}

// somewhere in controller
class ProfileController extends Controller
{
    public function actionAddFriend(int $id)
    {
        Yii::$app->user->identity->addFriend(User::findOne($id));
    }
}
```

> This is absolutely good way to extend almost any class of the extension. For more information you could
> check `Da\User\Bootstrap` class file to see what you have in your control.

© [2amigos](http://www.2amigos.us/) 2013-2019
