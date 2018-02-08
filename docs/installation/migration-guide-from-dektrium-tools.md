# Migration guide from Dektrium tools

yii2-usuario is 99% compatible with [dektrium](https://github.com/dektrium/) tools.

## Package removal

First of all you need to remove the old packages. Depending on your installation you 
need to remove one or both:
```
composer remove dektrium/yii2-user
composer remove dektrium/yii2-rbac
```

## Configuration

Configure the `config/console.php` stuff:

```php
        'authManager' => [
            'class' => 'yii\rbac\DbManager',
        ],
```

Configure the controller map for migrations

```php
    'controllerMap' => [
        'migrate' => [
            'class' => 'yii\console\controllers\MigrateController',
            'migrationNamespaces' => [
                'Da\User\Migration',
            ],
        ],
    ],
```

Remove the *modules > rbac* configuration parameter, and replace the value of *modules > user* with:
```php
    'user' => Da\User\Module::class,
```

In `config/web.php` remove *module > rbac* configuration and change the *modules > user* with: 
```php
	...
        'user' => [
            'class' => Da\User\Module::class,
            // Othe yii2-usuario configuration parameters
            'enableRegistration' => false,
        ],
    ...
```

*  If you had `modelMap` customization you have to replace them with `classMap`.
*  In your extended model replace the `BaseUser` inheritance from `dektrium\user\models\User` to `Da\User\Model\User`
*  If you had controller remapping replace the inheritance from `dektrium\user\controllers\XX` to `Da\User\Controller\XX`

## Rbac migrations

[yii2-rbac](https://github.com/dektrium/yii2-rbac) had a nice tool which are rbac migrations, which helped writing new permissions and roles.
There's no such feature in yii2-usuario, but in case you need to still apply them you can:

1.  create a migration component which basically it's the same as the original [Migration](https://github.com/dektrium/yii2-rbac/blob/master/migrations/Migration.php) object, with some minor changes. Copy the content below and save it in your `@app/components/RbacMigration.php`:
    ```php
    <?php

    /*
     * This file is part of the Dektrium project.
     *
     * (c) Dektrium project <http://github.com/dektrium/>
     *
     * For the full copyright and license information, please view the LICENSE
     * file that was distributed with this source code.
     */

    namespace app\components;

    use yii\rbac\DbManager;
    use yii\db\Migration;
    use yii\di\Instance;
    use yii\rbac\Item;
    use yii\rbac\Permission;
    use yii\rbac\Role;
    use yii\rbac\Rule;

    /**
     * Migration for applying new RBAC items.
     *
     * @author Dmitry Erofeev <dmeroff@gmail.com>
     */
    class RbacMigration extends Migration
    {
        /**
         * @var string|DbManager The auth manager component ID that this migration should work with.
         */
        public $authManager = 'authManager';

        /**
         * Initializes the migration.
         * This method will set [[authManager]] to be the 'authManager' application component, if it is `null`.
         */
        public function init()
        {
            parent::init();

            $this->authManager = Instance::ensure($this->authManager, DbManager::className());
        }

        /**
         * Creates new permission.
         *
         * @param  string      $name        The name of the permission
         * @param  string      $description The description of the permission
         * @param  string|null $ruleName    The rule associated with the permission
         * @param  mixed|null  $data        The additional data associated with the permission
         * @return Permission
         */
        protected function createPermission($name, $description = '', $ruleName = null, $data = null)
        {
            echo "    > create permission $name ...";
            $time       = microtime(true);
            $permission = $this->authManager->createPermission($name);

            $permission->description = $description;
            $permission->ruleName    = $ruleName;
            $permission->data        = $data;

            $this->authManager->add($permission);
            echo ' done (time: ' . sprintf('%.3f', microtime(true) - $time) . "s)\n";

            return $permission;
        }

        /**
         * Creates new role.
         *
         * @param  string      $name        The name of the role
         * @param  string      $description The description of the role
         * @param  string|null $ruleName    The rule associated with the role
         * @param  mixed|null  $data        The additional data associated with the role
         * @return Role
         */
        protected function createRole($name, $description = '', $ruleName = null, $data = null)
        {
            echo "    > create role $name ...";
            $time = microtime(true);
            $role = $this->authManager->createRole($name);

            $role->description = $description;
            $role->ruleName    = $ruleName;
            $role->data        = $data;

            $this->authManager->add($role);
            echo ' done (time: ' . sprintf('%.3f', microtime(true) - $time) . "s)\n";

            return $role;
        }

        /**
         * Creates new rule.
         *
         * @param  string       $ruleName   The name of the rule
         * @param  string|array $definition The class of the rule
         * @return Rule
         */
        protected function createRule($ruleName, $definition)
        {
            echo "    > create rule $ruleName ...";
            $time = microtime(true);

            if (is_array($definition)) {
                $definition['name'] = $ruleName;
            } else {
                $definition = [
                    'class' => $definition,
                    'name' => $ruleName,
                ];
            }

            /** @var Rule $rule */
            $rule = \Yii::createObject($definition);

            $this->authManager->add($rule);
            echo ' done (time: ' . sprintf('%.3f', microtime(true) - $time) . "s)\n";

            return $rule;
        }

        /**
         * Finds either role or permission or throws an exception if it is not found.
         *
         * @param  string $name
         * @return Permission|Role|null
         */
        protected function findItem($name)
        {
            $item = $this->authManager->getRole($name);

            if ($item instanceof Role) {
                return $item;
            }

            $item = $this->authManager->getPermission($name);

            if ($item instanceof Permission) {
                return $item;
            }

            return null;
        }

        /**
         * Finds the role or throws an exception if it is not found.
         *
         * @param  string $name
         * @return Role|null
         */
        protected function findRole($name)
        {
            $role = $this->authManager->getRole($name);

            if ($role instanceof Role) {
                return $role;
            }

            return null;
        }

        /**
         * Finds the permission or throws an exception if it is not found.
         *
         * @param  string $name
         * @return Permission|null
         */
        protected function findPermission($name)
        {
            $permission = $this->authManager->getPermission($name);

            if ($permission instanceof Permission) {
                return $permission;
            }

            return null;
        }

        /**
         * Removes auth item.
         *
         * @param string|Item $item Either item name or item instance to be removed.
         */
        protected function removeItem($item)
        {
            if (is_string($item)) {
                $item = $this->findItem($item);
            }

            echo "    > removing $item->name ...";
            $time = microtime(true);
            $this->authManager->remove($item);
            echo ' done (time: ' . sprintf('%.3f', microtime(true) - $time) . "s)\n";
        }

        /**
         * Adds child.
         * 
         * @param Item|string $parent Either name or Item instance which is parent
         * @param Item|string $child  Either name or Item instance which is child
         */
        protected function addChild($parent, $child)
        {
            if (is_string($parent)) {
                $parent = $this->findItem($parent);
            }

            if (is_string($child)) {
                $child = $this->findItem($child);
            }

            echo "    > adding $child->name as child to $parent->name ...";
            $time = microtime(true);
            $this->authManager->addChild($parent, $child);
            echo ' done (time: ' . sprintf('%.3f', microtime(true) - $time) . "s)\n";
        }

        /**
         * Assigns a role to a user.
         *
         * @param string|Role $role
         * @param string|int  $userId
         */
        protected function assign($role, $userId)
        {
            if (is_string($role)) {
                $role = $this->findRole($role);
            }

            echo "    > assigning $role->name to user $userId ...";
            $time = microtime(true);
            $this->authManager->assign($role, $userId);
            echo ' done (time: ' . sprintf('%.3f', microtime(true) - $time) . "s)\n";
        }

        /**
         * Updates role.
         *
         * @param  string|Role $role
         * @param  string      $description
         * @param  string      $ruleName
         * @param  mixed       $data
         * @return Role
         */
        protected function updateRole($role, $description = '', $ruleName = null, $data = null)
        {
            if (is_string($role)) {
                $role = $this->findRole($role);
            }

            echo "    > update role $role->name ...";
            $time = microtime(true);

            $role->description = $description;
            $role->ruleName    = $ruleName;
            $role->data        = $data;

            $this->authManager->update($role->name, $role);
            echo ' done (time: ' . sprintf('%.3f', microtime(true) - $time) . "s)\n";

            return $role;
        }

        /**
         * Updates permission.
         *
         * @param  string|Permission $permission
         * @param  string            $description
         * @param  string            $ruleName
         * @param  mixed             $data
         * @return Permission
         */
        protected function updatePermission($permission, $description = '', $ruleName = null, $data = null)
        {
            if (is_string($permission)) {
                $permission = $this->findPermission($permission);
            }

            echo "    > update permission $permission->name ...";
            $time = microtime(true);

            $permission->description = $description;
            $permission->ruleName    = $ruleName;
            $permission->data        = $data;

            $this->authManager->update($permission->name, $permission);
            echo ' done (time: ' . sprintf('%.3f', microtime(true) - $time) . "s)\n";

            return $permission;
        }

        /**
         * Updates rule.
         *
         * @param  string $ruleName
         * @param  string $className
         * @return Rule
         */
        protected function updateRule($ruleName, $className)
        {
            echo "    > update rule $ruleName ...";
            $time = microtime(true);

            /** @var Rule $rule */
            $rule = \Yii::createObject([
                'class' => $className,
                'name'  => $ruleName,
            ]);

            $this->authManager->update($ruleName, $rule);
            echo ' done (time: ' . sprintf('%.3f', microtime(true) - $time) . "s)\n";
            
            return $rule;
        }
    }

    ```

2.  change the inheritance of the `@app/rbac/migrations` files to `app\components\RbacMigration as Migration`

... and you're done! You can still apply your rbac migrations with `./yii migrate/up --migrationPath=@yii/rbac/migrations`.
