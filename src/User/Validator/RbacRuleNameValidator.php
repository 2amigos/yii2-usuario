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
use yii\rbac\Rule;
use yii\validators\Validator;

class RbacRuleNameValidator extends Validator
{
    use AuthManagerAwareTrait;

    /**
     * @var
     */
    public $previousName;

    /**
     * @inheritdoc
     */
    protected function validateValue($value)
    {
        if ($this->previousName !== $value) {
            $rule = $this->getAuthManager()->getRule($value);

            if ($rule instanceof Rule) {
                return [Yii::t('usuario', 'Rule name {0} is already in use', $value), []];
            }
        }
        return null;
    }
}
