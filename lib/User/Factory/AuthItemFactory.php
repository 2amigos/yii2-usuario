<?php
namespace Da\User\Factory;

use Yii;
use yii\rbac\Item;
use Exception;

class AuthItemFactory
{
    protected static $map = [
        Item::TYPE_ROLE => 'makeRole',
        Item::TYPE_PERMISSION => 'makePermission'
    ];

    /**
     * @param $name
     *
     * @return \yii\rbac\Permission
     */
    public static function makePermission($name)
    {
        return Yii::$app->getAuthManager()->createPermission($name);
    }

    /**
     * @param $name
     *
     * @return \yii\rbac\Role
     */
    public static function makeRole($name)
    {
        return Yii::$app->getAuthManager()->createRole($name);
    }

    /**
     * @param $type
     * @param $name
     *
     * @return \yii\rbac\Role|\yii\rbac\Permission
     * @throws Exception
     */
    public static function makeByType($type, $name)
    {
        if (array_key_exists($type, self::$map)) {
            return call_user_func([self::class, self::$map[$type]], $name);
        }

        throw new Exception('Unknown strategy type');
    }
}
