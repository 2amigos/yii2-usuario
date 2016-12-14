<?php
namespace Da\User\Validator;

use Da\User\Traits\AuthManagerTrait;
use Yii;
use yii\validators\Validator;

class RbacItemsValidator extends Validator
{
    use AuthManagerTrait;

    protected function validateValue($value)
    {
        if (!is_array($value)) {
            return [Yii::t('item', 'Invalid value'), []];
        }

        foreach ($value as $item) {
            if ($this->getAuthManager()->getItem($item) == null) {
                return [Yii::t('user', 'There is neither role nor permission with name "{0}"', [$item]), []];
            }
        }
    }

}
