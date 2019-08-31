First steps to use Yii2-usuario
===============================

After installing the extension and having configured everything, you need setup your application with the all the user related stuff, e.g.

* creating first users, roles, permissions, ...
* assigning permissions and roles to users
* extending your controllers with access restrictions  
* starting user management

## Creating your first user

There are several ways to do that:

* using migrations
* using the Command line [Console Commands](../installation/console-commands.md)

### Creating the first Administrator during a migration

This is helpful e.g. when using you Yii2 applicatio with Docker and need to start the Docker Container with basic user settings.

We will create an ```admin```user with a ```adminisrator``` role. 

Put this in your migration:

    class m... extends Migration
    {
        public function safeUp()
        {
            $auth = Yii::$app->authManager;
                
            // create a role named "administrator"
            $administratorRole = $auth->createRole('administrator');
            $administratorRole->description = 'Administrator';
            $auth->add($administratorRole);

            // create user "admin" with password "verysecret"
            $user = new \Da\User\Model\User([
                'scenario' => 'create', 
                'email' => "email@example.com", 
                'username' => "admin", 
                'password' => "verysecret"  // >6 characters!
            ]);
            $user->confirmed_at = time();
            $user->save();
            
            // assign role to our admin-user
            $auth->assign($administratorRole, $user->id);
        }

        public function safeDown()
        {
            $auth = Yii::$app->authManager;

            // delete admin-user and administrator role
            $administratorRole = $auth->getRole("xw-administrator");
            $user = \Da\User\Model\User::findOne(['name'=>"admin"]);
            $auth->revoke($administratorRole, $user->id);
            $user->delete();
        }

## User Management

Having setup the ```admin``` user you can start using user management at

    http://yourapp/index.php?r=user/admin

You should be prompted a login screen and the enter ```admin/verysecret```.

### Recommended Reading

It is helpful to basically understand how Yii2 does authantication. The you can get in Yii2-usuario more quickly.

- [The Definitive Guide to Yii 2.0: Authentication](https://www.yiiframework.com/doc/guide/2.0/en/security-authentication)


