<?php

/*
 * This file is part of the 2amigos/yii2-usuario project.
 *
 * (c) 2amigOS! <http://2amigos.us/>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Da\User\Factory;

use Exception;
use Yii;
use yii\base\InvalidArgumentException;
use yii\rbac\Item;
use yii\rbac\Permission;
use yii\rbac\Role;

class ItemFactory
{
    protected static $map = [
        Item::TYPE_ROLE => 'createRole',
        Item::TYPE_PERMISSION => 'createPermission',
    ];

    /**
     * @param string $name
     *
     * @return Permission
     */
    public static function createPermission(string $name): Permission
    {
        return Yii::$app->getAuthManager()->createPermission($name);
    }

    /**
     * @param string $name
     *
     * @return Role
     */
    public static function createRole(string $name): Role
    {
        return Yii::$app->getAuthManager()->createRole($name);
    }

    /**
     * @param $type
     * @param $name
     *
     * @return \yii\rbac\Role|Permission
     *
     * @throws Exception
     */
    public static function createByType(int $type, string $name): Item
    {
        if (array_key_exists($type, self::$map)) {
            return call_user_func([self::class, self::$map[$type]], $name);
        }

        throw new InvalidArgumentException('Unknown strategy type');
    }
}
