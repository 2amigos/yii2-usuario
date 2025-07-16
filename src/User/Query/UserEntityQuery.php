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
        $user = Yii::$app->user->identity;
        $module = $this->getModule();
        $maxAgeMonths = $module->maxPasskeyAge;
        $monthsBeforeDisplay = $module->passkeyExpirationTimeLimit;
        $supposedCreation = date('Y-m-d', strtotime('-' . ($maxAgeMonths-$monthsBeforeDisplay) . ' days'));
        $spanTime = date('Y-m-d',strtotime('-' . ($maxAgeMonths) . ' days'));
        $dateField = new \yii\db\Expression('COALESCE(last_used_at, created_at)');
        $this->andWhere(['<=', $dateField, $supposedCreation])
              ->andWhere(['>=', $dateField, $spanTime]);
        return $this;
    }
}
