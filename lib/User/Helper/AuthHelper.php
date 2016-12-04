<?php
namespace Da\User\Helper;

use Da\User\Module;
use Yii;

/**
 *
 * RoleHelper.php
 *
 * Date: 3/12/16
 * Time: 15:11
 * @author Antonio Ramirez <hola@2amigos.us>
 */
class AuthHelper
{
    /**
     * Checks whether
     *
     * @param $role
     *
     * @return bool
     */
    public function hasRole($userId, $role)
    {
        if (Yii::$app->getAuthManager()) {
            $roles = array_keys(Yii::$app->getAuthManager()->getRolesByUser($userId));

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
        $hasAdministratorPermissionName = Yii::$app->getAuthManager() && $module->administratorPermissionName
            ? Yii::$app->getUser()->can($module->administratorPermissionName)
            : false;

        return $hasAdministratorPermissionName || in_array($username, $module->administrators);
    }

}
