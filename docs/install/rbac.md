RBAC
====

This module comes with RBAC package by default. We haven't found ourselves that we didn't require at least an admin 
which doesn't require that level of security. Our projects always start with simple roles such as `admin` but later on 
our customers always ask for different levels of permissions for multiple roles. 

That is the reason why we include RBAC features by default, and whether you use it or not, you will have to apply 
Yii's `rbac` schema migrations or override the views so `PermissionController` and `RoleController` are never 
accessible.

We have added an access filter (`Da\User\Filter\AccessRuleFilter`) to allow you to work with those usernames you 
configure as administrators of your app via the 
[Module::administrators](configuration-options.md#administrators-type-array-default-) attribute.

> **Note**: Remember that you have to configure applications `authManager` with `'class' => 'Da\User\Component\AuthDbManagerComponent'`, 
> prior to even apply the rbac migrations! 

How to Use `AccessRuleFilter`
-----------------------------

The following is a fragment on how the `Da\User\Controller\AdminController` has configured the filter:

```php
// ...

use Da\User\Filter\AccessRuleFilter;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

// ...

class AdminController extends Controller
{
    // ...
    
    
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['post'],
                    'confirm' => ['post'],
                    'block' => ['post'],
                ],
            ],
            'access' => [
                'class' => AccessControl::class,
                'ruleConfig' => [
                    'class' => AccessRuleFilter::class,
                ],
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['admin'],
                    ],
                ],
            ],
        ];
    }
    
    
    // ... 
}
```


© [2amigos](http://www.2amigos.us/) 2013-2019
