<?php

/*
 * This file is part of the 2amigos/yii2-usuario project.
 *
 * (c) 2amigOS! <http://2amigos.us/>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Da\User\Controller;

use Da\User\Model\Permission;
use Da\User\Search\PermissionSearch;
use yii\web\NotFoundHttpException;

class PermissionController extends AbstractAuthItemController
{
    /**
     * {@inheritdoc}
     */
    protected function getModelClass()
    {
        return Permission::class;
    }

    /**
     * {@inheritdoc}
     */
    protected function getSearchModelClass()
    {
        return PermissionSearch::class;
    }

    /**
     * {@inheritdoc}
     *
     * @throws NotFoundHttpException
     */
    protected function getItem($name)
    {
        $authItem = $this->authHelper->getPermission($name);

        if ($authItem !== null) {
            return $authItem;
        }

        throw new NotFoundHttpException();
    }
}
