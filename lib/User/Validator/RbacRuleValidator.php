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

use ReflectionClass;
use Exception;
use yii\validators\Validator;
use Yii;

class RbacRuleValidator extends Validator
{
    protected function validateValue($value)
    {
        try {
            $class = new ReflectionClass($value);
        } catch (Exception $e) {
            return [Yii::t('usuario', 'Class "{0}" does not exist', $value), []];
        }

        if ($class->isInstantiable() == false) {
            return [Yii::t('usuario', 'Rule class can not be instantiated'), []];
        }
        if ($class->isSubclassOf('\yii\rbac\Rule') == false) {
            return [Yii::t('usuario', 'Rule class must extend "yii\rbac\Rule"'), []];
        }
    }
}
