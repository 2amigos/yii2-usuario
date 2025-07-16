<?php

namespace Da\User\Command;

use yii\console\Controller;
use Da\User\Model\UserEntity;
use yii\db\Expression;
use Da\User\Module;

class DeleteExpiredUserEntityController extends Controller
{
    public function actionRun()
    {
        $fakeId=0;
        $module = new Module($fakeId);
        $maxAgeMonths = $module->maxPasskeyAge;
        $monthsBeforeDisplay = $module->passkeyExpirationTimeLimit;
        $supposedCreation = date('Y-m-d', strtotime('-' . ($maxAgeMonths - $monthsBeforeDisplay) . ' days'));
        $spanTime = date('Y-m-d', strtotime('-' . $maxAgeMonths . ' days'));
        $dateField = new Expression('COALESCE(last_used_at, created_at)');
        $expired = UserEntity::find()
            ->andWhere(['<=', $dateField, $supposedCreation])
            ->andWhere(['>=', $dateField, $spanTime])
            ->all();

        foreach ($expired as $passkey) {
            $passkey->delete();
        }
        return 0;
    }
}
