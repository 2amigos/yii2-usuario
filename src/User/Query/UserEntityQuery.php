<?php

namespace Da\User\Query;

use Da\User\Model\UserEntity;
use Da\User\Traits\ModuleAwareTrait;
use Yii;
use yii\db\ActiveQuery;

class UserEntityQuery extends ActiveQuery
{
    use ModuleAwareTrait;

    public function expiring()
    {
        $daysTr = Yii::t('usuario','days');

        $module = $this->getModule();
        $maxDaysPasskey = $module->maxPasskeyAge;
        $daysBeforeDisplay = $module->passkeyExpirationTimeLimit; //number of days before the message about an expiring passkey is shown
        $supposedCreation = date('Y-m-d', strtotime('-' . ($maxDaysPasskey-$daysBeforeDisplay) . $daysTr));
        $spanTime = date('Y-m-d',strtotime('-' . ($maxDaysPasskey) . $daysTr));
        $dateField = new \yii\db\Expression('COALESCE(last_used_at, created_at)');
        $this->andWhere(['<=', $dateField, $supposedCreation])
              ->andWhere(['>=', $dateField, $spanTime]);
        return $this;
    }
}
