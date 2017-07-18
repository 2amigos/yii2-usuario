How to Switch Identities with a user
====================================

If you are admin, you can impersonate (switch identities with another user). This action is taken when you, as an admin, 
needs to check the panel of a user. Whether for support or simply checking whether is using correctly your system 
according to your terms and conditions. 

The default view of this module provides the mechanism on the grid that displays the users by placing a user icon on 
its actions column. Once, clicked, it will call the **/user/admin/switch-identity** and make the transition. Then will 
redirect you to the index page (`goHome()`) and is there where you will have to take the correspondent actions to 
display the views appropriated for that user. 
 
How to rollback to admin user
-----------------------------
The way to do it is by calling that action again. The link to do that could be written like this: 

```php
$module = Yii::$app->getModule('user');

if(Yii::$app->session->has($module->switchIdentitySessionKey)) {
   echo Html::a('Switch to Admin', ['/user/admin/switch-identity'], ['data-method' => 'post']);
}
```

Check the [switchIdentitySessionKey](../installation/configuration-options.md#switchidentitysessionkey) documentation  
for further information regarding this configuration option. 

© [2amigos](http://www.2amigos.us/) 2013-2017
