<?php
namespace Da\User\Model;

use Da\User\Query\SocialNetworkAccountQuery;
use yii\db\ActiveRecord;

class SocialNetworkAccount extends ActiveRecord
{
    /**
     * @return SocialNetworkAccountQuery
     */
    public static function find()
    {
        return new SocialNetworkAccountQuery(static::class);
    }
}
