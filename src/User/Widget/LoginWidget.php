<?php

/*
 * This file is part of the 2amigos/yii2-usuario project.
 *
 * (c) 2amigOS! <http://2amigos.us/>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Da\User\Widget;

use Da\User\Form\LoginForm;
use Da\User\Traits\ModuleAwareTrait;
use Yii;
use yii\base\Widget;

/**
 * @deprecated this seems to be unused by this module. To be deleted in future!
 */
class LoginWidget extends Widget
{
    use ModuleAwareTrait;
    public $validate = true;

    public function run()
    {
        return $this->render(
            $this->getModule()->viewPath .'/widgets/login/form',
            [
                'model' => Yii::createObject(LoginForm::class),
            ]
        );
    }
}
