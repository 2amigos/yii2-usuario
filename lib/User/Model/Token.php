<?php
namespace Da\User\Model;

use Da\User\Query\TokenQuery;
use yii\db\ActiveRecord;


class Token extends ActiveRecord
{
    const TYPE_CONFIRMATION      = 0;
    const TYPE_RECOVERY          = 1;
    const TYPE_CONFIRM_NEW_EMAIL = 2;
    const TYPE_CONFIRM_OLD_EMAIL = 3;

    /**
     * @return TokenQuery
     */
    public static function find()
    {
        return new TokenQuery(static::class);
    }
}
