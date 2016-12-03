<?php
namespace Da\User\Helper;

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
        if (Yii::$app->authManager) {
            $roles = array_keys(Yii::$app->authManager->getRolesByUser($userId));

            return in_array($role, $roles, true);
        }

        return false;
    }
}
