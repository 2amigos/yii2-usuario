<?php
namespace Da\User\Model;

use Da\User\Query\ProfileQuery;
use yii\db\ActiveRecord;


class Profile extends ActiveRecord
{
    /**
     * @return ProfileQuery
     */
    public static function find()
    {
        return new ProfileQuery(static::class);
    }
}
