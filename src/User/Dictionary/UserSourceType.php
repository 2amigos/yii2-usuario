<?php

namespace Da\User\Dictionary;

use yii\helpers\ArrayHelper;

class UserSourceType
{
    const LOCAL = 'LOCAL';
    const LDAP = 'LDAP';

    /**
     * Returns an array that contains the codes for the dictionary and their common name. It's useful to be used for
     * dropdowns and selects.
     * @return array
     */
    public static function all()
    {
        return [
            static::LOCAL => \Yii::t('usuario',  'Local'),
            static::LDAP => \Yii::t('usuario', 'LDAP'),
        ];
    }

    /**
     * Returns the dictionary value for the given code
     * @param $key
     * @return string|null
     * @throws \Exception
     */
    public static function get($key)
    {
        return ArrayHelper::getValue(static::all(), $key);
    }


}
