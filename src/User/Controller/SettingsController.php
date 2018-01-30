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

use Da\User\Contracts\MailChangeStrategyInterface;
use Da\User\Event\ProfileEvent;
use Da\User\Event\SocialNetworkConnectEvent;
use Da\User\Event\UserEvent;
use Da\User\Form\SettingsForm;
use Da\User\Model\Profile;
use Da\User\Model\SocialNetworkAccount;
use Da\User\Model\User;
use Da\User\Module;
use Da\User\Query\ProfileQuery;
use Da\User\Query\SocialNetworkAccountQuery;
use Da\User\Query\UserQuery;
use Da\User\Service\EmailChangeService;
use Da\User\Service\TwoFactorQrCodeUriGeneratorService;
use Da\User\Traits\ContainerAwareTrait;
use Da\User\Validator\AjaxRequestModelValidator;
use Da\User\Validator\TwoFactorCodeValidator;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class SettingsController extends Controller
{
    use ContainerAwareTrait;

    /**
     * {@inheritdoc}
     */
    public $defaultAction = 'profile';

    protected $profileQuery;
    protected $userQuery;
    protected $socialNetworkAccountQuery;

    /**
     * SettingsController constructor.
     *
     * @param string                    $id
     * @param Module                    $module
     * @param ProfileQuery              $profileQuery
     * @param UserQuery                 $userQuery
     * @param SocialNetworkAccountQuery $socialNetworkAccountQuery
     * @param array                     $config
     */
    public function __construct(
        $id,
        Module $module,
        ProfileQuery $profileQuery,
        UserQuery $userQuery,
        SocialNetworkAccountQuery $socialNetworkAccountQuery,
        array $config = []
    ) {
        $this->profileQuery = $profileQuery;
        $this->userQuery = $userQuery;
        $this->socialNetworkAccountQuery = $socialNetworkAccountQuery;
        parent::__construct($id, $module, $config);
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'disconnect' => ['post'],
                    'delete' => ['post'],
                    'two-factor-disable' => ['post']
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => [
                            'profile',
                            'account',
                            'networks',
                            'disconnect',
                            'delete',
                            'two-factor',
                            'two-factor-enable',
                            'two-factor-disable'
                        ],
                        'roles' => ['@'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['confirm'],
                        'roles' => ['?', '@'],
                    ],
                ],
            ],
        ];
    }

    public function actionProfile()
    {
        $profile = $this->profileQuery->whereUserId(Yii::$app->user->identity->getId())->one();

        if ($profile === null) {
            $profile = $this->make(Profile::class);
            $profile->link('user', Yii::$app->user->identity);
        }

        $event = $this->make(ProfileEvent::class, [$profile]);

        $this->make(AjaxRequestModelValidator::class, [$profile])->validate();

        if ($profile->load(Yii::$app->request->post())) {
            $this->trigger(UserEvent::EVENT_BEFORE_PROFILE_UPDATE, $event);
            if ($profile->save()) {
                Yii::$app->getSession()->setFlash('success', Yii::t('usuario', 'Your profile has been updated'));
                $this->trigger(UserEvent::EVENT_AFTER_PROFILE_UPDATE, $event);

                return $this->refresh();
            }
        }

        return $this->render(
            'profile',
            [
                'model' => $profile,
            ]
        );
    }

    public function actionAccount()
    {
        /** @var SettingsForm $form */
        $form = $this->make(SettingsForm::class);
        $event = $this->make(UserEvent::class, [$form->getUser()]);

        $this->make(AjaxRequestModelValidator::class, [$form])->validate();

        if ($form->load(Yii::$app->request->post())) {
            $this->trigger(UserEvent::EVENT_BEFORE_ACCOUNT_UPDATE, $event);

            if ($form->save()) {
                Yii::$app->getSession()->setFlash(
                    'success',
                    Yii::t('usuario', 'Your account details have been updated')
                );
                $this->trigger(UserEvent::EVENT_AFTER_ACCOUNT_UPDATE, $event);

                return $this->refresh();
            }
        }

        return $this->render(
            'account',
            [
                'model' => $form,
            ]
        );
    }

    public function actionConfirm($id, $code)
    {
        $user = $this->userQuery->whereId($id)->one();

        if ($user === null || MailChangeStrategyInterface::TYPE_INSECURE === $this->module->emailChangeStrategy) {
            throw new NotFoundHttpException();
        }
        $event = $this->make(UserEvent::class, [$user]);

        $this->trigger(UserEvent::EVENT_BEFORE_CONFIRMATION, $event);
        if ($this->make(EmailChangeService::class, [$code, $user])->run()) {
            $this->trigger(UserEvent::EVENT_AFTER_CONFIRMATION, $event);
        }

        return $this->redirect(['account']);
    }

    public function actionNetworks()
    {
        return $this->render(
            'networks',
            [
                'user' => Yii::$app->user->identity,
            ]
        );
    }

    public function actionDisconnect($id)
    {
        /** @var SocialNetworkAccount $account */
        $account = $this->socialNetworkAccountQuery->whereId($id)->one();

        if ($account === null) {
            throw new NotFoundHttpException();
        }
        if ($account->user_id !== Yii::$app->user->id) {
            throw new ForbiddenHttpException();
        }
        $event = $this->make(SocialNetworkConnectEvent::class, [Yii::$app->user->identity, $account]);

        $this->trigger(SocialNetworkConnectEvent::EVENT_BEFORE_DISCONNECT, $event);
        $account->delete();
        $this->trigger(SocialNetworkConnectEvent::EVENT_AFTER_DISCONNECT, $event);

        return $this->redirect(['networks']);
    }

    public function actionDelete()
    {
        if (!$this->module->allowAccountDelete) {
            throw new NotFoundHttpException(Yii::t('usuario', 'Not found'));
        }

        /** @var User $user */
        $user = Yii::$app->user->identity;
        $event = $this->make(UserEvent::class, [$user]);
        Yii::$app->user->logout();

        $this->trigger(UserEvent::EVENT_BEFORE_DELETE, $event);
        $user->delete();
        $this->trigger(UserEvent::EVENT_AFTER_DELETE, $event);

        Yii::$app->session->setFlash('info', Yii::t('usuario', 'Your account has been completely deleted'));

        return $this->goHome();
    }

    public function actionTwoFactor($id)
    {
        /** @var User $user */
        $user = $this->userQuery->whereId($id)->one();

        if (null === $user) {
            throw new NotFoundHttpException();
        }

        $uri = $this->make(TwoFactorQrCodeUriGeneratorService::class, [$user])->run();

        return $this->renderAjax('two-factor', ['id' => $id, 'uri' => $uri]);
    }

    public function actionTwoFactorEnable($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        /** @var User $user */
        $user = $this->userQuery->whereId($id)->one();

        if (null === $user) {
            return [
                'success' => false,
                'message' => Yii::t('usuario', 'User not found.')
            ];
        }
        $code = Yii::$app->request->get('code');

        $success = $this
            ->make(TwoFactorCodeValidator::class, [$user, $code, $this->module->twoFactorAuthenticationCycles])
            ->validate();

        $success = $success && $user->updateAttributes(['auth_tf_enabled' => '1']);

        return [
            'success' => $success,
            'message' => $success
                ? Yii::t('usuario', 'Two factor authentication successfully enabled.')
                : Yii::t('usuario', 'Verification failed. Please, enter new code.')
        ];
    }

    public function actionTwoFactorDisable($id)
    {
        /** @var User $user */
        $user = $this->userQuery->whereId($id)->one();

        if (null === $user) {
            throw new NotFoundHttpException();
        }

        if ($user->updateAttributes(['auth_tf_enabled' => '0'])) {
            Yii::$app
                ->getSession()
                ->setFlash('success', Yii::t('usuario', 'Two factor authentication has been disabled.'));
        } else {
            Yii::$app
                ->getSession()
                ->setFlash('danger', Yii::t('usuario', 'Unable to disable Two factor authentication.'));
        }

        $this->redirect(['account']);
    }
}
