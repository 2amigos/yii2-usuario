<?php

namespace Da\User\Command;

use yii\console\Controller;
use Da\User\Model\UserEntity;
use yii\db\Expression;
use Da\User\Traits\ModuleAwareTrait;

class DeleteExpiredPasskeysController extends Controller
{
    use ModuleAwareTrait;

    public function actionRun()
    {
        $module = $this->getModule();

        $maxAgeMonths = $module->maxPasskeyAge;
        $monthsBeforeDisplay = $module->passkeyExpirationTimeLimit;

        $supposedCreation = date('Y-m-d', strtotime('-' . ($maxAgeMonths - $monthsBeforeDisplay) . ' days'));
        $spanTime = date('Y-m-d', strtotime('-' . $maxAgeMonths . ' days'));

        $dateField = new Expression('COALESCE(last_used_at, created_at)');

        $expired = UserEntity::find()
            ->andWhere(['<=', $dateField, $supposedCreation])
            ->andWhere(['>=', $dateField, $spanTime])
            ->all();

        $count = 0;
        foreach ($expired as $passkey) {
            if ($passkey->delete()) {
                $count++;
            }
        }

        echo "$count expired passkeys deleted.\n";

        return 0;
    }
}
