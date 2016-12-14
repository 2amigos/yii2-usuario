<?php
namespace Da\User\Controller;

use Da\User\Model\Role;
use Da\User\Search\RoleSearch;
use yii\web\NotFoundHttpException;

class RoleController extends AbstractAuthItemController
{
    /**
     * @inheritdoc
     */
    protected function getModelClass()
    {
        return Role::class;
    }

    /**
     * @inheritdoc
     */
    protected function getSearchModelClass()
    {
        return RoleSearch::class;
    }

    /**
     * @inheritdoc
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
