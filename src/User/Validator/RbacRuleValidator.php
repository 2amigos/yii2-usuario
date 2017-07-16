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

use Da\User\Traits\ContainerAwareTrait;
use Exception;
use Yii;
use yii\rbac\Rule;
use yii\validators\Validator;

class RbacRuleValidator extends Validator
{
    use ContainerAwareTrait;

    protected function validateValue($value)
    {
        try {
            $rule = $this->make($value);

            if (!($rule instanceof Rule)) {
                return [Yii::t('usuario', 'Rule class must extend "yii\\rbac\\Rule".'), []];
            }
        } catch (Exception $e) {
            return [Yii::t('usuario', 'Authentication rule class {0} can not be instantiated', $value), []];
        }
    }
}
