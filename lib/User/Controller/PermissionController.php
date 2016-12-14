<?php

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
