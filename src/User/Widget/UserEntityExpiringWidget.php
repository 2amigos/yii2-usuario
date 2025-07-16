<?php

namespace Da\User\Widget;

use Da\User\Model\User;
use Da\User\Model\UserEntity;
use Da\User\Traits\ModuleAwareTrait;
use Yii;
use yii\base\Widget;

class UserEntityExpiringWidget extends Widget
{
    use ModuleAwareTrait;

    public function run()
    {
        parent::run();
        /** @var User $user */
        $module = $this->getModule();
        $user = Yii::$app->user->identity;
        if(!isset($user) || $module->enablePasskeyExpiringNotification){
            return '';
        } else{
            $expiringPasskeys = $user->getUserEntities()->expiring()->all();
            if (count($expiringPasskeys) >= 1) {
                $popupData = $this->generatePopupData($expiringPasskeys);
                echo $this->render('user-entity/pop-up-expiration', [
                    'popupData' => $popupData,
                    'count' => count($popupData),
                ]);
            }
        }
    }


    /**
     * @param UserEntity[] $expiringPasskeys
     * @return array
     * @throws \DateMalformedStringException
     */
    public function generatePopupData($expiringPasskeys)
    {
        $popupData = [];

        $maxAgeMonths = $this->module->maxPasskeyAge;

        foreach ($expiringPasskeys as $passkey) {
            $lastUsedAt = $passkey->last_used_at ?: $passkey->created_at ?: date('Y-m-d');
            $createdAt = new \DateTime($lastUsedAt);
            $expirationDate = (clone $createdAt)->modify("+$maxAgeMonths days");
            $now = new \DateTime();
            $daysLeft = $now->diff($expirationDate)->days;
            $popupData[] = [
                'id' => $passkey->id,
                'name' => $passkey->name ?? '-',
                'daysLeft' => $daysLeft,
                'expirationDate' => $expirationDate->format('Y-m-d'),
            ];
        }
        return $popupData;
    }
}
