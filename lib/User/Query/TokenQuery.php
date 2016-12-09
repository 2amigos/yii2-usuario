<?php
namespace Da\User\Query;

use Da\User\Model\Token;
use yii\db\ActiveQuery;

class TokenQuery extends ActiveQuery
{
    public function whereIsRecoveryType($userId, $code)
    {
        return $this->andWhere(['user_id' => $userId, 'code' => $code, 'type' => Token::TYPE_RECOVERY]);
    }

    public function whereIsConfirmationType($userId, $code)
    {
        return $this->andWhere(['user_id' => $userId, 'code' => $code, 'type' => Token::TYPE_CONFIRM_NEW_EMAIL]);
    }
}
