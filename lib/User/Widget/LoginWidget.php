<?php
namespace Da\User\Widget;

use Da\User\Form\LoginForm;
use Yii;
use yii\base\Widget;

class LoginWidget extends Widget
{
    public $validate = true;

    public function run()
    {
        return $this->render(
            'login',
            [
                'model' => Yii::createObject(LoginForm::class)
            ]
        );
    }
}
