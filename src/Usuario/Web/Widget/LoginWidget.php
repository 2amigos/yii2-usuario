<?php

/*
 * This file is part of the 2amigos/yii2-usuario project.
 *
 * (c) 2amigOS! <http://2amigos.us/>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Da\Usuario\Web\Widget;

use Da\User\Form\LoginForm;
use Yii;
use yii\base\Widget;

class LoginWidget extends Widget
{
    public $view = '';
    public $validate = true;

    /**
     * @return string
     * @throws \yii\base\InvalidConfigException
     */
    public function run()
    {
        return $this->render(
            $this->view,
            [
                'model' => Yii::createObject(LoginForm::class),
            ]
        );
    }
}
