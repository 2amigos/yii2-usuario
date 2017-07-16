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

class RbacRuleExistsValidator extends Validator
{
    use AuthManagerAwareTrait;

    protected function validateValue($value)
    {
        $rule = $this->getAuthManager()->getRule($value);

        if (!$rule) {
            return [Yii::t('usuario', 'Rule {0} does not exists', $value), []];
        }
    }
}
