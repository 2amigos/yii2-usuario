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

use Da\User\Filter\AccessRuleFilter;
use Da\User\Helper\AuthHelper;
use Da\User\Model\AbstractAuthItem;
use Da\User\Module;
use Da\User\Service\AuthItemEditionService;
use Da\User\Traits\ContainerAwareTrait;
use Da\User\Validator\AjaxRequestModelValidator;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;

abstract class AbstractAuthItemController extends Controller
{
    use ContainerAwareTrait;

    protected $modelClass;
    protected $searchModelClass;
    protected $authHelper;

    /**
     * AbstractAuthItemController constructor.
     *
     * @param string     $id
     * @param Module     $module
     * @param AuthHelper $authHelper
     * @param array      $config
     */
    public function __construct($id, Module $module, AuthHelper $authHelper, array $config = [])
    {
        $this->authHelper = $authHelper;
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
                'ruleConfig' => [
                    'class' => AccessRuleFilter::class,
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

    public function actionIndex()
    {
        $searchModel = $this->make($this->getSearchModelClass());

        return $this->render(
            'index',
            [
                'searchModel' => $searchModel,
                'dataProvider' => $searchModel->search(Yii::$app->request->get()),
            ]
        );
    }

    public function actionCreate()
    {
        /** @var AbstractAuthItem $model */
        $model = $this->make($this->getModelClass(), [], ['scenario' => 'create']);

        $this->make(AjaxRequestModelValidator::class, [$model])->validate();

        if ($model->load(Yii::$app->request->post())) {
            if ($this->make(AuthItemEditionService::class, [$model])->run()) {
                Yii::$app
                    ->getSession()
                    ->setFlash('success', Yii::t('usuario', 'Authorization item successfully created.'));

                return $this->redirect(['index']);
            }
            Yii::$app->getSession()->setFlash('danger', Yii::t('usuario', 'Unable to create authorization item.'));
        }

        return $this->render(
            'create',
            [
                'model' => $model,
                'unassignedItems' => $this->authHelper->getUnassignedItems($model),
            ]
        );
    }

    public function actionUpdate($name)
    {
        $authItem = $this->getItem($name);

        /** @var AbstractAuthItem $model */
        $model = $this->make($this->getModelClass(), [], ['scenario' => 'update', 'item' => $authItem]);

        $this->make(AjaxRequestModelValidator::class, [$model])->validate();

        if ($model->load(Yii::$app->request->post())) {
            if ($this->make(AuthItemEditionService::class, [$model])->run()) {
                Yii::$app
                    ->getSession()
                    ->setFlash('success', Yii::t('usuario', 'Authorization item successfully updated.'));

                return $this->redirect(['index']);
            }
            Yii::$app->getSession()->setFlash('danger', Yii::t('usuario', 'Unable to update authorization item.'));
        }

        return $this->render(
            'update',
            [
                'model' => $model,
                'unassignedItems' => $this->authHelper->getUnassignedItems($model),
            ]
        );
    }

    public function actionDelete($name)
    {
        $item = $this->getItem($name);

        if ($this->authHelper->remove($item)) {
            Yii::$app->getSession()->setFlash('success', Yii::t('usuario', 'Authorization item successfully removed.'));
        } else {
            Yii::$app->getSession()->setFlash('success', Yii::t('usuario', 'Unable to remove authorization item.'));
        }

        return $this->redirect(['index']);
    }

    /**
     * The fully qualified class name of the model.
     *
     * @return string
     */
    abstract protected function getModelClass();

    /**
     * The fully qualified class name of the search model.
     *
     * @return string
     */
    abstract protected function getSearchModelClass();

    /**
     * Returns the an auth item.
     *
     * @param string $name
     *
     * @return \yii\rbac\Role|\yii\rbac\Permission|\yii\rbac\Rule
     */
    abstract protected function getItem($name);
}
