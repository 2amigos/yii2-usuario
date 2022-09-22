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
use Da\User\Event\GdprEvent;
use Da\User\Event\ProfileEvent;
use Da\User\Event\SocialNetworkConnectEvent;
use Da\User\Event\UserEvent;
use Da\User\Form\GdprDeleteForm;
use Da\User\Form\SettingsForm;
use Da\User\Helper\SecurityHelper;
use Da\User\Model\Profile;
use Da\User\Model\SocialNetworkAccount;
use Da\User\Model\User;
use Da\User\Module;
use Da\User\Query\ProfileQuery;
use Da\User\Query\SocialNetworkAccountQuery;
use Da\User\Query\UserQuery;
use Da\User\Search\SessionHistorySearch;
use Da\User\Service\EmailChangeService;
use Da\User\Service\SessionHistory\TerminateUserSessionsService;
use Da\User\Service\TwoFactorEmailCodeGeneratorService;
use Da\User\Service\TwoFactorQrCodeUriGeneratorService;
use Da\User\Service\TwoFactorSmsCodeGeneratorService;
use Da\User\Traits\ContainerAwareTrait;
use Da\User\Traits\ModuleAwareTrait;
use Da\User\Validator\AjaxRequestModelValidator;
use Da\User\Validator\TwoFactorCodeValidator;
use Da\User\Validator\TwoFactorEmailValidator;
use Da\User\Validator\TwoFactorTextMessageValidator;
use Yii;
use yii\base\DynamicModel;
use yii\base\InvalidParamException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class SettingsController extends Controller
{
    use ContainerAwareTrait;
    use ModuleAwareTrait;

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
                'class' => VerbFilter::class,
                'actions' => [
                    'disconnect' => ['post'],
                    'delete' => ['post'],
                    'two-factor-disable' => ['post'],
                    'terminate-sessions' => ['post'],
                ],
            ],
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => [
                            'profile',
                            'account',
                            'export',
                            'networks',
                            'privacy',
                            'gdpr-consent',
                            'gdpr-delete',
                            'disconnect',
                            'delete',
                            'two-factor',
                            'two-factor-enable',
                            'two-factor-disable',
                            'two-factor-mobile-phone'
                        ],
                        'roles' => ['@'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['confirm'],
                        'roles' => ['?', '@'],
                    ],
                    [
                        'allow' => $this->getModule()->enableSessionHistory,
                        'actions' => ['session-history', 'terminate-sessions'],
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * @throws \yii\base\InvalidConfigException
     * @return string|Response
     */
    public function actionProfile()
    {
        $profile = $this->profileQuery->whereUserId(Yii::$app->user->identity->getId())->one();

        if ($profile === null) {
            $profile = $this->make(Profile::class);
            $profile->link('user', Yii::$app->user->identity);
        }

        /**
        *
        *
        * @var ProfileEvent $event
        */
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

    /**
     * @throws NotFoundHttpException
     * @return string
     */
    public function actionPrivacy()
    {
        if (!$this->module->enableGdprCompliance) {
            throw new NotFoundHttpException();
        }
        return $this->render(
            'privacy',
            [
            'module' => $this->module
            ]
        );
    }

    /**
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws \yii\base\Exception
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\StaleObjectException
     * @throws ForbiddenHttpException
     * @return string|Response
     */
    public function actionGdprDelete()
    {
        if (!$this->module->enableGdprCompliance) {
            throw new NotFoundHttpException();
        }
        /**
        *
        *
        * @var GdprDeleteForm $form
        */
        $form = $this->make(GdprDeleteForm::class);

        $user = $form->getUser();
        /* @var $event GdprEvent */
        $event = $this->make(GdprEvent::class, [$user]);

        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            $this->trigger(GdprEvent::EVENT_BEFORE_DELETE, $event);

            if ($event->isValid) {
                Yii::$app->user->logout();
                //Disconnect social networks
                $networks = $this->socialNetworkAccountQuery->where(['user_id' => $user->id])->all();
                foreach ($networks as $network) {
                    $this->disconnectSocialNetwork($network->id);
                }

                /* @var $security SecurityHelper */
                $security = $this->make(SecurityHelper::class);
                $anonymReplacement = $this->module->gdprAnonymizePrefix . $user->id;

                $user->updateAttributes(
                    [
                    'email' => $anonymReplacement . "@example.com",
                    'username' => $anonymReplacement,
                    'gdpr_deleted' => 1,
                    'blocked_at' => time(),
                    'auth_key' => $security->generateRandomString()
                    ]
                );
                $user->profile->updateAttributes(
                    [
                    'public_email' => $anonymReplacement . "@example.com",
                    'name' => $anonymReplacement,
                    'gravatar_email' => $anonymReplacement . "@example.com",
                    'location' => $anonymReplacement,
                    'website' => $anonymReplacement . ".tld",
                    'bio' => Yii::t('usuario', 'Deleted by GDPR request')
                    ]
                );
            }
            $this->trigger(GdprEvent::EVENT_AFTER_DELETE, $event);

            Yii::$app->session->setFlash('info', Yii::t('usuario', 'Your personal information has been removed'));

            return $this->goHome();
        }

        return $this->render(
            'gdpr-delete',
            [
            'model' => $form,
            ]
        );
    }

    public function actionGdprConsent()
    {
        /**
        *
        *
        * @var User $user
        */
        $user = Yii::$app->user->identity;
        if ($user->gdpr_consent) {
            return $this->redirect(['profile']);
        }
        $model = new DynamicModel(['gdpr_consent']);
        $model->addRule('gdpr_consent', 'boolean');
        $model->addRule('gdpr_consent', 'default', ['value' => 0, 'skipOnEmpty' => false]);
        $model->addRule(
            'gdpr_consent',
            'compare',
            [
            'compareValue' => true,
            'message' => Yii::t('usuario', 'Your consent is required to work with this site'),
            'when' => function () {
                return $this->module->enableGdprCompliance;
            },
            ]
        );
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $user->updateAttributes(
                [
                'gdpr_consent' => 1,
                'gdpr_consent_date' => time(),
                ]
            );
            return $this->redirect(['profile']);
        }

        return $this->render(
            'gdpr-consent',
            [
            'model' => $model,
            'gdpr_consent_hint' => $this->module->getConsentMessage(),
            ]
        );
    }

    /**
     * Exports the data from the current user in a mechanical readable format (csv). Properties exported can be defined
     * in the module configuration.
     *
     * @throws NotFoundHttpException if gdpr compliance is not enabled
     * @throws \Exception
     * @throws \Throwable
     */
    public function actionExport()
    {
        if (!$this->module->enableGdprCompliance) {
            throw new NotFoundHttpException();
        }
        try {
            $properties = $this->module->gdprExportProperties;
            $user = Yii::$app->user->identity;
            $data = [$properties, []];

            $formatter = Yii::$app->formatter;
            // override the default html-specific format for nulls
            $formatter->nullDisplay = "";

            foreach ($properties as $property) {
                $data[1][] = $formatter->asText(ArrayHelper::getValue($user, $property));
            }

            array_walk($data[0], function (&$value, $key) {
                $splitted = explode('.', $value);
                $value = array_pop($splitted);
            });

            Yii::$app->response->headers->removeAll();
            Yii::$app->response->headers->add('Content-type', 'text/csv');
            Yii::$app->response->headers->add('Content-Disposition', 'attachment;filename=gdpr-data.csv');
            Yii::$app->response->send();
            $f = fopen('php://output', 'w');
            foreach ($data as $line) {
                fputcsv($f, $line);
            }
        } catch (\Exception $e) {
            throw $e;
        } catch (\Throwable $e) {
            throw $e;
        }
    }

    public function actionAccount()
    {
        /**
*
         *
 * @var SettingsForm $form
*/
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
        $this->disconnectSocialNetwork($id);
        return $this->redirect(['networks']);
    }

    public function actionDelete()
    {
        if (!$this->module->allowAccountDelete) {
            throw new NotFoundHttpException(Yii::t('usuario', 'Not found'));
        }

        /**
        *
        *
        * @var User $user
        */
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
        if (!$this->module->enableTwoFactorAuthentication) {
            throw new ForbiddenHttpException(Yii::t('usuario', 'Application not configured for two factor authentication.'));
        }

        if ($id != Yii::$app->user->id) {
            throw new ForbiddenHttpException();
        }

        $choice = Yii::$app->request->post('choice');
        /** @var User $user */
        $user = $this->userQuery->whereId($id)->one();

        if (null === $user) {
            throw new NotFoundHttpException();
        }

        switch ($choice) {
            case 'google-authenticator':
                $uri = $this->make(TwoFactorQrCodeUriGeneratorService::class, [$user])->run();
                return $this->renderAjax('two-factor', ['id' => $id, 'uri' => $uri]);
            case 'email':
                $emailCode = $this->make(TwoFactorEmailCodeGeneratorService::class, [$user])->run();
                return $this->renderAjax('two-factor-email', ['id' => $id, 'code' => $emailCode]);
            case 'sms':
                // get mobile phone, if exists
                $mobilePhone = $user->getAuthTfMobilePhone();
                $smsCode = $this->make(TwoFactorSmsCodeGeneratorService::class, [$user])->run();
                return $this->renderAjax('two-factor-sms', ['id' => $id, 'code' => $smsCode, 'mobilePhone' => $mobilePhone]);
            default:
                throw new InvalidParamException("Invalid 2FA choice");
        }
    }

    public function actionTwoFactorEnable($id)
    {
        if (!$this->module->enableTwoFactorAuthentication) {
            throw new ForbiddenHttpException(Yii::t('usuario', 'Application not configured for two factor authentication.'));
        }

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
        $module = Yii::$app->getModule('user');
        $validators = $module->twoFactorAuthenticationValidators;
        $choice = Yii::$app->request->get('choice');
        $codeDurationTime = ArrayHelper::getValue($validators, $choice.'.codeDurationTime', 300);
        $class = ArrayHelper::getValue($validators, $choice.'.class');

        $object = $this
            ->make($class, [$user, $code, $this->module->twoFactorAuthenticationCycles]);
        $success = $object->validate();
        $success = $success && $user->updateAttributes(['auth_tf_enabled' => '1','auth_tf_type' => $choice]);
        $message = $success ? $object->getSuccessMessage() : $object->getUnsuccessMessage($codeDurationTime);

        return [
            'success' => $success,
            'message' => $message
        ];
    }

    public function actionTwoFactorDisable($id)
    {
        if (!$this->module->enableTwoFactorAuthentication) {
            throw new ForbiddenHttpException(Yii::t('usuario', 'Application not configured for two factor authentication.'));
        }

        if ($id != Yii::$app->user->id) {
            throw new ForbiddenHttpException();
        }

        /**
        * @var User $user
        */
        $user = $this->userQuery->whereId($id)->one();

        if (null === $user) {
            throw new NotFoundHttpException();
        }

        if ($user->updateAttributes(['auth_tf_enabled' => '0', 'auth_tf_key' => null])) {
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

    /**
     * Display list session history.
     */
    public function actionSessionHistory()
    {
        $searchModel = new SessionHistorySearch([
            'user_id' => Yii::$app->user->id,
        ]);
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('session-history', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Terminate all session user
     */
    public function actionTerminateSessions()
    {
        $this->make(TerminateUserSessionsService::class, [Yii::$app->user->id])->run();

        return $this->redirect(['session-history']);
    }

    public function actionTwoFactorMobilePhone($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        /**
        *
        *
        * @var User $user
        */
        $user = $this->userQuery->whereId($id)->one();

        if (null === $user) {
            return [
                'success' => false,
                'message' => Yii::t('usuario', 'User not found.')
            ];
        }
        $mobilePhone = Yii::$app->request->get('mobilephone');
        $currentMobilePhone = $user->getAuthTfMobilePhone();
        $success = false;
        if ($currentMobilePhone == $mobilePhone) {
            $success = true;
        } else {
            $success = $user->updateAttributes(['auth_tf_mobile_phone' => $mobilePhone]);
            $success = $success && $this->make(TwoFactorSmsCodeGeneratorService::class, [$user])->run();
        }

        return [
                    'success' => $success,
                    'message' => $success
                    ? Yii::t('usuario', 'Mobile phone number successfully enabled.')
                    : Yii::t('usuario', 'Error while enabling SMS two factor authentication. Please reload the page.'),
                ];
    }

    /**
     * @param $id
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     * @throws \Exception
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    protected function disconnectSocialNetwork($id)
    {
        /**
        *
        *
        * @var SocialNetworkAccount $account
        */
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
    }
}
