<?php
namespace Da\User\Controller;

use Da\User\Event\FormEvent;
use Da\User\Factory\MailFactory;
use Da\User\Form\RecoveryForm;
use Da\User\Model\Token;
use Da\User\Query\TokenQuery;
use Da\User\Query\UserQuery;
use Da\User\Service\PasswordRecoveryService;
use Da\User\Traits\ContainerTrait;
use Da\User\Traits\ModuleTrait;
use Da\User\Validator\AjaxRequestModelValidator;
use Yii;
use yii\base\Module;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class RecoveryController extends Controller
{
    use ModuleTrait;
    use ContainerTrait;

    protected $userQuery;
    protected $tokenQuery;

    /**
     * RecoveryController constructor.
     *
     * @param string $id
     * @param Module $module
     * @param UserQuery $userQuery
     * @param TokenQuery $tokenQuery
     * @param array $config
     */
    public function __construct($id, Module $module, UserQuery $userQuery, TokenQuery $tokenQuery, array $config)
    {
        $this->userQuery = $userQuery;
        $this->tokenQuery = $tokenQuery;
        parent::__construct($id, $module, $config);
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['request', 'reset'],
                        'roles' => ['?']
                    ],
                ],
            ],
        ];
    }

    public function actionRequest()
    {
        if (!$this->getModule()->allowPasswordRecovery) {
            throw new NotFoundHttpException();
        }

        /** @var RecoveryForm $form */
        $form = $this->make(RecoveryForm::class, ['scenario' => RecoveryForm::SCENARIO_REQUEST]);

        $event = $this->make(FormEvent::class, [$form]);

        $this->make(AjaxRequestModelValidator::class, $form)->validate();

        $this->trigger(FormEvent::EVENT_BEFORE_REQUEST, $event);

        if ($form->load(Yii::$app->request->post())) {
            $mailService = MailFactory::makeRecoveryMailerService($form->email);

            if ($this->make(PasswordRecoveryService::class, [$form->email, $mailService])->run()) {
                $this->trigger(FormEvent::EVENT_AFTER_REQUEST, $event);

                return $this->render(
                    'message',
                    [
                        'title' => Yii::t('user', 'Recovery message sent'),
                        'module' => $this->getModule(),
                    ]
                );
            }
        }

        return $this->render('request', ['model' => $form,]);
    }
}
