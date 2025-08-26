<?php

namespace Da\User\Command;

use yii\console\Controller;
use Da\User\Model\UserEntity;
use yii\db\Expression;
use Da\User\Module;

class UserEntityController extends Controller
{
    public function actionDeleteExpiredPasskeys()
    {
        $fakeId=0;
        $module = new Module($fakeId);
        $maxDaysPasskey = $module->maxPasskeyAge;
        $supposedCreation = date('Y-m-d', strtotime('-' . $maxDaysPasskey . ' days'));
        $dateField = new Expression('COALESCE(last_used_at, created_at)');
        $expired = UserEntity::find()
            ->andWhere(['<=', $dateField, $supposedCreation])
            ->all();

        foreach ($expired as $passkey) {
            $passkey->delete();
        }
        return 0;
    }
}
