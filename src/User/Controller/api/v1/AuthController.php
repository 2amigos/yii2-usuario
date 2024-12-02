<?php

/*
 * This file is part of the 2amigos/yii2-usuario project.
 *
 * (c) 2amigOS! <http://2amigos.us/>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Da\User\Controller\api\v1;

use sizeg\jwt\JwtHttpBearerAuth;
use Yii;
use yii\filters\Cors;
use yii\rbac\ManagerInterface;
use yii\rest\Controller as RestController;

class AuthController extends RestController
{
    /**
     * {@inheritdoc}
     */
    public function behaviors(): array
    {
        $behaviors = parent::behaviors();
        $behaviors['corsFilter'] = [
            'class' => Cors::class,
        ];
        $behaviors['authenticator'] = [
            'class' => JwtHttpBearerAuth::class,
        ];
        return $behaviors;
    }

    /**
     * {@inheritdoc}
     */
    protected function verbs(): array
    {
        $verbs = parent::verbs();
        $verbs['roles'] = ['GET'];
        $verbs['roles-by-user'] = ['GET'];
        return $verbs;
    }

    /**
     * Returns all the roles.
     * @return array
     */
    public function actionRoles(): array
    {
        return Yii::$app->authManager->getRoles();
    }

    /**
     * Returns all the roles for the current user.
     * @return array
     */
    public function actionRolesByUser(): array
    {
        $authManager = Yii::$app->authManager;
        $userId = Yii::$app->user->id;

        // Get roles directly assigned to the user
        $roles = $authManager->getRolesByUser($userId);
        $allRoles = [];

        // Process each role to fetch its children
        foreach ($roles as $role) {
            $this->fetchRoleChildren($role->name, $authManager, $allRoles);
        }

        return $allRoles;
    }

    /**
     * Recursively fetches a role and its children.
     * @param string $roleName
     * @param ManagerInterface $authManager
     * @param array $allRoles
     */
    private function fetchRoleChildren(string $roleName, ManagerInterface $authManager, array &$allRoles): void
    {
        if (!isset($allRoles[$roleName])) {
            $allRoles[$roleName] = $authManager->getRole($roleName);
            $children = $authManager->getChildren($roleName);
            foreach ($children as $child) {
                $this->fetchRoleChildren($child->name, $authManager, $allRoles);
            }
        }
    }
}
