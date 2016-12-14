<?php

namespace Da\User\Traits;

use Da\User\Module;
use Yii;

/**
 * @property-read Module $module
 */
trait ModuleTrait
{
    /**
     * @return \Da\User\Module
     */
    public function getModule()
    {
        return Yii::$app->getModule('user');
    }
}
