<?php

namespace Da\User\Widget;

use Da\User\Model\User;
use Da\User\Traits\ModuleAwareTrait;
use Yii;
use yii\base\Widget;

class UserEntityPasskeyWidget extends Widget
{
    use ModuleAwareTrait;
    public function run()
    {
        parent::run();
        if(Yii::$app->session->get('passkey_pop-up')===true)
        {
            echo $this->render('user-entity/pop-up');
            Yii::$app->session->remove('passkey_pop-up');
        }
    }
}
