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

use Da\User\Model\Role;
use Da\User\Search\RoleSearch;
use yii\web\NotFoundHttpException;

class RoleController extends AbstractAuthItemController
{
    /**
     * {@inheritdoc}
     */
    protected function getModelClass()
    {
        return Role::class;
    }

    /**
     * {@inheritdoc}
     */
    protected function getSearchModelClass()
    {
        return RoleSearch::class;
    }

    /**
     * {@inheritdoc}
     *
     * @throws NotFoundHttpException
     */
    protected function getItem($name)
    {
        $authItem = $this->authHelper->getRole($name);

        if ($authItem !== null) {
            return $authItem;
        }

        throw new NotFoundHttpException();
    }
}
