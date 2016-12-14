<?php
namespace Da\User\Traits;

use Da\User\Component\AuthDbManagerComponent;
use Yii;

trait AuthManagerTrait
{
    /**
     * @return AuthDbManagerComponent|\yii\rbac\ManagerInterface
     */
    public function getAuthManager() {

        return Yii::$app->getAuthManager();
    }
}
