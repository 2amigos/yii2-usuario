<?php

/*
 * This file is part of the 2amigos/yii2-usuario project.
 *
 * (c) 2amigOS! <http://2amigos.us/>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Da\User\Validator;

use Da\User\Traits\AuthManagerAwareTrait;
use Yii;
use yii\validators\Validator;

class RbacItemsValidator extends Validator
{
    use AuthManagerAwareTrait;

    protected function validateValue($value)
    {
        if (!is_array($value)) {
            return [Yii::t('usuario', 'Invalid value'), []];
        }

        foreach ($value as $item) {
            if ($this->getAuthManager()->getItem($item) === null) {
                return [Yii::t('usuario', 'There is neither role nor permission with name "{0}"', [$item]), []];
            }
        }
    }
}
