<?php

/*
 * This file is part of the 2amigos/yii2-usuario project.
 *
 * (c) 2amigOS! <http://2amigos.us/>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Da\User\Controller\api\v1;

use Da\User\Event\FormEvent;
use Da\User\Form\LoginForm;
use Da\User\Traits\ContainerAwareTrait;
use Da\User\Traits\ModuleAwareTrait;
use Yii;
use yii\filters\auth\CompositeAuth;
use yii\rest\Controller;
use yii\web\UnauthorizedHttpException;

class SecurityController extends Controller
{
    use ContainerAwareTrait;
    use ModuleAwareTrait;

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        // Remove authentication filter added by parent (if any)
        unset($behaviors['authenticator']);

        $behaviors['authenticator'] = [
            'class' => CompositeAuth::class,
            'except' => ['login'],
        ];

        return $behaviors;
    }

    public function actionLogin()
    {
        /**
         * @var LoginForm $form
         */
        $form = $this->make(LoginForm::class);

        /**
         * @var FormEvent $event
         */
        $event = $this->make(FormEvent::class, [$form]);

        $form->load(Yii::$app->getRequest()->getBodyParams(), '');

        // TODO: implement 2FA authentication
        // if ($this->module->enableTwoFactorAuthentication && $form->validate()) {
        //     $user = $form->getUser();

        //     if ($user->auth_tf_enabled) {
        //         Yii::$app->session->set('credentials', ['login' => $form->login, 'pwd' => $form->password]);
        //         return $this->redirect(['confirm']);
        //     }
        // }

        $this->trigger(FormEvent::EVENT_BEFORE_LOGIN, $event);
        if ($form->login()) {
            $form->getUser()->updateAttributes([
                'last_login_at' => time(),
                'last_login_ip' => $this->module->disableIpLogging ? '127.0.0.1' : Yii::$app->request->getUserIP(),
            ]);

            $this->trigger(FormEvent::EVENT_AFTER_LOGIN, $event);

            return [
                'token' => $form->getUser()->getAccessToken(),
            ];
        }
        $this->trigger(FormEvent::EVENT_FAILED_LOGIN, $event);

        throw new UnauthorizedHttpException("Login failed. You are unauthorized to perform actions");
    }
}
