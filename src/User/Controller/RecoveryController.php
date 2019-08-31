<?php

/*
 * This file is part of the 2amigos/yii2-usuario project.
 *
 * (c) 2amigOS! <http://2amigos.us/>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Da\User\Controller;

use Da\User\Event\FormEvent;
use Da\User\Event\ResetPasswordEvent;
use Da\User\Factory\MailFactory;
use Da\User\Form\RecoveryForm;
use Da\User\Model\Token;
use Da\User\Module;
use Da\User\Query\TokenQuery;
use Da\User\Query\UserQuery;
use Da\User\Service\PasswordRecoveryService;
use Da\User\Service\ResetPasswordService;
use Da\User\Traits\ContainerAwareTrait;
use Da\User\Traits\ModuleAwareTrait;
use Da\User\Validator\AjaxRequestModelValidator;
use Yii;
use yii\base\InvalidConfigException;
use yii\base\InvalidParamException;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class RecoveryController extends Controller
{
    use ContainerAwareTrait;
    use ModuleAwareTrait;

    protected $userQuery;
    protected $tokenQuery;

    /**
     * RecoveryController constructor.
     *
     * @param string     $id
     * @param Module     $module
     * @param UserQuery  $userQuery
     * @param TokenQuery $tokenQuery
     * @param array      $config
     */
    public function __construct($id, Module $module, UserQuery $userQuery, TokenQuery $tokenQuery, array $config = [])
    {
        $this->userQuery = $userQuery;
        $this->tokenQuery = $tokenQuery;
        parent::__construct($id, $module, $config);
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['request', 'reset'],
                        'roles' => ['?'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Displays / handles user password recovery request.
     *
     * @throws NotFoundHttpException
     * @throws InvalidConfigException
     * @throws InvalidParamException
     * @return string
     *
     */
    public function actionRequest()
    {
        if (!$this->module->allowPasswordRecovery) {
            throw new NotFoundHttpException();
        }

        /** @var RecoveryForm $form */
        $form = $this->make(RecoveryForm::class, [], ['scenario' => RecoveryForm::SCENARIO_REQUEST]);

        $event = $this->make(FormEvent::class, [$form]);

        $this->make(AjaxRequestModelValidator::class, [$form])->validate();

        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            $this->trigger(FormEvent::EVENT_BEFORE_REQUEST, $event);

            $mailService = MailFactory::makeRecoveryMailerService($form->email);

            if ($this->make(PasswordRecoveryService::class, [$form->email, $mailService])->run()) {
                $this->trigger(FormEvent::EVENT_AFTER_REQUEST, $event);
            }

            return $this->render(
                '/shared/message',
                [
                    'title' => Yii::t('usuario', 'Recovery message sent'),
                    'module' => $this->module,
                ]
            );
        }

        return $this->render('request', ['model' => $form]);
    }

    /**
     * Displays / handles user password reset.
     *
     * @param $id
     * @param $code
     *
     * @throws NotFoundHttpException
     * @throws InvalidConfigException
     * @throws InvalidParamException
     * @return string
     *
     */
    public function actionReset($id, $code)
    {
        if (!$this->module->allowPasswordRecovery && !$this->module->allowAdminPasswordRecovery) {
            throw new NotFoundHttpException();
        }
        /** @var Token $token */
        $token = $this->tokenQuery->whereUserId($id)->whereCode($code)->whereIsRecoveryType()->one();
        /** @var ResetPasswordEvent $event */
        $event = $this->make(ResetPasswordEvent::class, [$token]);

        $this->trigger(ResetPasswordEvent::EVENT_BEFORE_TOKEN_VALIDATE, $event);

        if ($token === null || $token->getIsExpired() || $token->user === null) {
            Yii::$app->session->setFlash(
                'danger',
                Yii::t('usuario', 'Recovery link is invalid or expired. Please try requesting a new one.')
            );

            return $this->render(
                '/shared/message',
                [
                    'title' => Yii::t('usuario', 'Invalid or expired link'),
                    'module' => $this->module,
                ]
            );
        }

        /** @var RecoveryForm $form */
        $form = $this->make(RecoveryForm::class, [], ['scenario' => RecoveryForm::SCENARIO_RESET]);
        $event = $event->updateForm($form);

        $this->make(AjaxRequestModelValidator::class, [$form])->validate();

        if ($form->load(Yii::$app->getRequest()->post())) {
            if ($this->make(ResetPasswordService::class, [$form->password, $token->user])->run()) {
                $this->trigger(ResetPasswordEvent::EVENT_AFTER_RESET, $event);

                Yii::$app->session->setFlash('success', Yii::t('usuario', 'Password has been changed'));

                return $this->render(
                    '/shared/message',
                    [
                        'title' => Yii::t('usuario', 'Password has been changed'),
                        'module' => $this->module,
                    ]
                );
            }
        }

        return $this->render('reset', ['model' => $form]);
    }
}
