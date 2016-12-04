<?php
namespace Da\User\Controller;

use Da\User\Event\UserEvent;
use Da\User\Filter\AccessRuleFilter;
use Da\User\Model\User;
use Da\User\Service\UserCreateService;
use Da\User\Traits\ContainerTrait;
use Da\User\Validator\AjaxRequestModelValidator;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use Yii;


class AdminController extends Controller
{
    use ContainerTrait;

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                    'confirm' => ['post'],
                    'block' => ['post'],
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'ruleConfig' => [
                    'class' => AccessRuleFilter::className(),
                ],
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['admin'],
                    ],
                ],
            ],
        ];
    }

    public function actionCreate()
    {
        /** @var User $user */
        $user = $this->make(User::class, ['scenario' => 'create']);

        /** @var UserEvent $event */
        $event = $this->make(UserEvent::class, [$user]);

        $this->make(AjaxRequestModelValidator::class, [$user])->validate();

        $this->trigger(UserEvent::EVENT_BEFORE_CREATE, $event);

        if($user->load(Yii::$app->request->post())) {
            /** @var UserCreateService $userCreateService */
            $userCreateService = $this->make(UserCreateService::class, [$user]);
            $userCreateService->run();

            $this->trigger(UserEvent::EVENT_AFTER_CREATE, $event);

            return $this->redirect(['update', 'id' => $user->id]);
        }

        return $this->render('create', [
            'user' => $user,
        ]);
    }
}
