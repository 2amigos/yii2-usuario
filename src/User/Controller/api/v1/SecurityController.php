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

use Da\User\Controller\api\v1\models\ApiUser;
use Da\User\Event\FormEvent;
use Da\User\Form\LoginForm;
use Da\User\Model\User;
use Da\User\Traits\ContainerAwareTrait;
use Yii;
use yii\base\InvalidConfigException;
use yii\base\InvalidParamException;
use yii\filters\Cors;
use yii\rest\Controller;

/**
 * Controller that provides REST APIs to manage users.
 * This controller is equivalent to `Da\User\Controller\AdminController`.
 *
 * TODO:
 * - `Info` and `SwitchIdentity` actions were not developed yet.
 * - `Assignments` action implements only GET method (POST method not developed yet).
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
        // Remove the (default) authentication filter
        unset($behaviors['authenticator']);

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
     * @return ApiUser
     *@throws InvalidParamException
     * @throws InvalidConfigException
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->getIsGuest()) {
            return [];
        }

        /**
         * @var LoginForm $form
         */
        $form = $this->make(LoginForm::class);

        /**
         * @var FormEvent $event
         */
        $event = $this->make(FormEvent::class, [$form]);

        if ($form->load(Yii::$app->request->post(), '')) {
            $form->validate();
            $this->trigger(FormEvent::EVENT_BEFORE_LOGIN, $event);
            if ($form->login()) {
                $form->getUser()->updateAttributes([
                    'last_login_at' => time(),
                    'last_login_ip' => $this->module->disableIpLogging ? '127.0.0.1' : Yii::$app->request->getUserIP(),
                ]);

                $this->trigger(FormEvent::EVENT_AFTER_LOGIN, $event);
            }
            $this->trigger(FormEvent::EVENT_FAILED_LOGIN, $event);
        }

        return User::findOne($form->getUser()->id);
    }
}
