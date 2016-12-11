<?php
namespace Da\User\Controller;

use Da\User\Query\ProfileQuery;
use yii\base\Module;
use yii\filters\AccessControl;
use yii\web\Controller;
use Yii;
use yii\web\NotFoundHttpException;

class ProfileController extends Controller
{
    protected $profileQuery;

    /**
     * ProfileController constructor.
     *
     * @param string $id
     * @param Module $module
     * @param ProfileQuery $profileQuery
     * @param array $config
     */
    public function __construct($id, Module $module, ProfileQuery $profileQuery, array $config = [])
    {
        $this->profileQuery = $profileQuery;
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
                        'actions' => ['index'],
                        'roles' => ['@']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['show'],
                        'roles' => ['?', '@']
                    ],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        return $this->redirect(['show', 'id' => Yii::$app->user->getId()]);
    }

    public function actionShow($id)
    {
        $profile = $this->profileQuery->whereId($id)->one();
        if ($profile === null) {
            throw new NotFoundHttpException();
        }

        return $this->render(
            'show',
            [
                'profile' => $profile,
            ]
        );
    }

}
