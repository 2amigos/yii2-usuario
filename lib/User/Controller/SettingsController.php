<?php
namespace Da\User\Controller;

use Da\User\Contracts\MailChangeStrategyInterface;
use Da\User\Event\FormEvent;
use Da\User\Event\ProfileEvent;
use Da\User\Event\UserEvent;
use Da\User\Form\SettingsForm;
use Da\User\Model\Profile;
use Da\User\Module;
use Da\User\Query\ProfileQuery;
use Da\User\Query\SocialNetworkAccountQuery;
use Da\User\Query\UserQuery;
use Da\User\Service\EmailChangeService;
use Da\User\Traits\ContainerTrait;
use Da\User\Validator\AjaxRequestModelValidator;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use Yii;
use yii\web\NotFoundHttpException;


class SettingsController extends Controller
{
    use ContainerTrait;

    protected $profileQuery;
    protected $userQuery;
    protected $socialNetworkAccountQuery;

    /**
     * @inheritdoc
     */
    public $defaultAction = 'profile';

    /**
     * SettingsController constructor.
     *
     * @param string $id
     * @param Module $module
     * @param ProfileQuery $profileQuery
     * @param UserQuery $userQuery
     * @param SocialNetworkAccountQuery $socialNetworkAccountQuery
     * @param array $config
     */
    public function __construct(
        $id,
        Module $module,
        ProfileQuery $profileQuery,
        UserQuery $userQuery,
        SocialNetworkAccountQuery $socialNetworkAccountQuery,
        array $config
    ) {
        $this->profileQuery = $profileQuery;
        $this->userQuery = $userQuery;
        $this->socialNetworkAccountQuery = $socialNetworkAccountQuery;
        parent::__construct($id, $module, $config);
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'disconnect' => ['post'],
                    'delete' => ['post'],
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['profile', 'account', 'networks', 'disconnect', 'delete'],
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
        $profile = $this->profileQuery->whereId(Yii::$app->user->identity->getId())->one();

        if ($profile === null) {
            $profile = $this->make(Profile::class);
            $profile->link('user', Yii::$app->user->identity);
        }

        $event = $this->make(ProfileEvent::class, [$profile]);

        $this->make(AjaxRequestModelValidator::class, [$profile])->validate();

        if ($profile->load(Yii::$app->request->post())) {
            $this->trigger(UserEvent::EVENT_BEFORE_PROFILE_UPDATE, $event);
            if ($profile->save()) {
                Yii::$app->getSession()->setFlash('success', Yii::t('user', 'Your profile has been updated'));
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
        $form = $this->make(SettingsForm::class);
        $event = $this->make(FormEvent::class, [$form]);

        $this->make(AjaxRequestModelValidator::class, [$form])->validate();

        if($form->load(Yii::$app->request->post())) {
            $this->trigger(UserEvent::EVENT_BEFORE_ACCOUNT_UPDATE, $event);
            if($form->save()) {
                Yii::$app->getSession()->setFlash('success', Yii::t('user', 'Your account details have been updated-'));
                $this->trigger(UserEvent::EVENT_AFTER_ACCOUNT_UPDATE, $event);

                return $this->refresh();
            }
        }

        return $this->render('account', [
            'model' => $form,
        ]);
    }

    public function actionConfirm($id, $code) {
        $user = $this->userQuery->whereId($id)->one();

        if ($user === null || $this->module->emailChangeStrategy == MailChangeStrategyInterface::TYPE_INSECURE) {
            throw new NotFoundHttpException();
        }
        $event = $this->make(UserEvent::class, [$user]);

        $this->trigger(UserEvent::EVENT_BEFORE_CONFIRMATION, $event);
        if($this->make(EmailChangeService::class, [$code, $user])->run()) {
            $this->trigger(UserEvent::EVENT_AFTER_CONFIRMATION, $event);
        }

        return $this->redirect(['account']);
    }
}
