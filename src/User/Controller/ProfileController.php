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

use Da\User\Query\ProfileQuery;
use Yii;
use yii\base\Module;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class ProfileController extends Controller
{
    protected $profileQuery;

    /**
     * ProfileController constructor.
     *
     * @param string       $id
     * @param Module       $module
     * @param ProfileQuery $profileQuery
     * @param array        $config
     */
    public function __construct($id, Module $module, ProfileQuery $profileQuery, array $config = [])
    {
        $this->profileQuery = $profileQuery;
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
                        'actions' => ['index'],
                        'roles' => ['@'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['show'],
                        'roles' => ['?', '@'],
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
        $profile = $this->profileQuery->whereUserId($id)->one();

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
