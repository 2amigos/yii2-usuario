<?php
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
            return [Yii::t('user', 'Class "{0}" does not exist', $value), []];
        }

        if ($class->isInstantiable() == false) {
            return [Yii::t('user', 'Rule class can not be instantiated'), []];
        }
        if ($class->isSubclassOf('\yii\rbac\Rule') == false) {
            return [Yii::t('user', 'Rule class must extend "yii\rbac\Rule"'), []];
        }
    }
}
