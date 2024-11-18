<?php

/*
 * This file is part of the 2amigos/yii2-usuario project.
 *
 * (c) 2amigOS! <http://2amigos.us/>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Da\User\Controller\rest\v1;

use Da\User\Event\FormEvent;
use Da\User\Form\LoginForm;
use Da\User\Model\User;
use Da\User\Traits\ContainerAwareTrait;
use sizeg\jwt\JwtHttpBearerAuth;
use Yii;
use yii\base\InvalidConfigException;
use yii\base\InvalidParamException;
use yii\filters\Cors;
use yii\rest\Controller;

/**
 * Controller that provides REST APIs to login via:
 * - JWT (JSON Web Token)
 * - login and password
 */
class SecurityController extends Controller
{
    use ContainerAwareTrait;

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();
        Yii::$app->user->enableSession = false;
        Yii::$app->user->loginUrl = null;
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors(): array
    {
        $behaviors = parent::behaviors();

        // Set JWT authenticator
        $behaviors['authenticator'] = [
            'class' => JwtHttpBearerAuth::class,
            'optional' => [
                'login'
            ],
        ];

        // Cors filter
        $behaviors['corsFilter'] = [
            'class' => Cors::class,
        ];

        return $behaviors;
    }

    /**
     * {@inheritdoc}
     */
    protected function verbs(): array
    {
        // Get parent verbs
        $verbs = parent::verbs();

        // Add new verbs and return
        $verbs['login'] = ['POST'];
        return $verbs;
    }

    /**
     * Controller action responsible for handling login page and actions.
     *
     * @return array
     * @throws InvalidParamException
     * @throws InvalidConfigException
     */
    public function actionLogin(): array
    {
        if (!Yii::$app->user->getIsGuest()) {
            return [
                'success' => false,
                'message' => Yii::t('usuario', 'User is already logged in'),
            ];
        }

        /**
         * @var LoginForm $form
         */
        $form = $this->make(LoginForm::class);

        /**
         * @var FormEvent $event
         */
        $event = $this->make(FormEvent::class, [$form]);

        $token = null;
        $uid = null;
        if ($form->load(Yii::$app->request->post(), '')) {
            $form->validate();
            $this->trigger(FormEvent::EVENT_BEFORE_LOGIN, $event);
            if ($form->login()) {
                $form->getUser()->updateAttributes([
                    'last_login_at' => time(),
                    'last_login_ip' => $this->module->disableIpLogging ? '127.0.0.1' : Yii::$app->request->getUserIP(),
                ]);

                $this->trigger(FormEvent::EVENT_AFTER_LOGIN, $event);
                $user = $form->getUser();
            }
            $this->trigger(FormEvent::EVENT_FAILED_LOGIN, $event);
        }

        return array_merge(
            User::findOne($user->id)->attributes,
            [
                'token' => (string)$user->getJwt(),
            ]
        );
    }
}
