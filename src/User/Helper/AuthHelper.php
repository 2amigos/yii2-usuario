<?php

/*
 * This file is part of the 2amigos/yii2-usuario project.
 *
 * (c) 2amigOS! <http://2amigos.us/>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Da\User\Helper;

use Da\User\Model\AbstractAuthItem;
use Da\User\Module;
use Da\User\Traits\AuthManagerAwareTrait;
use Yii;
use yii\helpers\ArrayHelper;
use yii\rbac\Permission;
use yii\rbac\Role;
use yii\rbac\Rule;

class AuthHelper
{
    use AuthManagerAwareTrait;

    /**
     * Checks whether a user has certain role.
     *
     * @param $userId
     * @param $role
     *
     * @return bool
     */
    public function hasRole($userId, $role)
    {
        if ($this->getAuthManager()) {
            $roles = array_keys($this->getAuthManager()->getRolesByUser($userId));

            return in_array($role, $roles, true);
        }

        return false;
    }

    /**
     * @param $username
     *
     * @return bool
     */
    public function isAdmin($username)
    {
        /** @var Module $module */
        $module = Yii::$app->getModule('user');
        $hasAdministratorPermissionName = $this->getAuthManager() && $module->administratorPermissionName
            ? Yii::$app->getUser()->can($module->administratorPermissionName)
            : false;

        return $hasAdministratorPermissionName || in_array($username, $module->administrators, false);
    }

    /**
     * @param $name
     *
     * @return null|\yii\rbac\Item|Permission
     */
    public function getPermission($name)
    {
        return $this->getAuthManager()->getPermission($name);
    }

    /**
     * @param $name
     *
     * @return null|\yii\rbac\Item|Role
     */
    public function getRole($name)
    {
        return $this->getAuthManager()->getRole($name);
    }

    /**
     * Removes a role, permission or rule from the RBAC system.
     *
     * @param Role|Permission|Rule $object
     *
     * @return bool whether the role, permission or rule is successfully removed
     */
    public function remove($object)
    {
        return $this->getAuthManager()->remove($object);
    }

    /**
     * @param AbstractAuthItem $model
     *
     * @return array
     */
    public function getUnassignedItems(AbstractAuthItem $model)
    {
        $excludeItems = $model->item !== null ? [$model->item->name] : [];
        $type = $model->getType() === Permission::TYPE_PERMISSION ? Permission::TYPE_PERMISSION : null;
        $items = $this->getAuthManager()->getItems($type, $excludeItems);

        return ArrayHelper::map(
            $items,
            'name',
            function ($item) {
                return empty($item->description) ? $item->name : "{$item->name} ({$item->description})";
            }
        );
    }
}
