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
use Da\User\Model\Rule;
use Da\User\Search\RuleSearch;
use Da\User\Service\AuthRuleEditionService;
use Da\User\Traits\AuthManagerAwareTrait;
use Da\User\Traits\ContainerAwareTrait;
use Da\User\Validator\AjaxRequestModelValidator;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class RuleController extends Controller
{
    use AuthManagerAwareTrait;
    use ContainerAwareTrait;

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
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
        /** @var RuleSearch $searchModel */
        $searchModel = $this->make(RuleSearch::class);
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render(
            'index',
            [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]
        );
    }

    public function actionCreate()
    {
        $model = $this->make(Rule::class, [], ['scenario' => 'create', 'className' => \yii\rbac\Rule::class]);

        $this->make(AjaxRequestModelValidator::class, [$model])->validate();

        if ($model->load(Yii::$app->request->post())) {
            if ($this->make(AuthRuleEditionService::class, [$model])->run()) {
                Yii::$app->getSession()->setFlash('success', Yii::t('usuario', 'Authorization rule has been added.'));

                return $this->redirect(['index']);
            }
            Yii::$app->getSession()->setFlash('success', Yii::t('usuario', 'Unable to create new authorization rule.'));
        }

        return $this->render(
            'create',
            [
                'model' => $model
            ]
        );
    }

    public function actionUpdate($name)
    {
        /** @var Rule $model */
        $model = $this->make(Rule::class, [], ['scenario' => 'update']);
        $rule = $this->findRule($name);

        $model->setAttributes(
            [
                'previousName' => $name,
                'name' => $rule->name,
                'className' => get_class($rule)
            ]
        );

        $this->make(AjaxRequestModelValidator::class, [$model])->validate();

        if ($model->load(Yii::$app->request->post())) {
            if ($this->make(AuthRuleEditionService::class, [$model])->run()) {
                Yii::$app->getSession()->setFlash('success', Yii::t('usuario', 'Authorization rule has been updated.'));

                return $this->redirect(['index']);
            }
            Yii::$app->getSession()->setFlash('success', Yii::t('usuario', 'Unable to update authorization rule.'));
        }

        return $this->render(
            'update',
            [
                'model' => $model,
            ]
        );
    }

    public function actionDelete($name)
    {
        $rule = $this->findRule($name);

        $this->getAuthManager()->remove($rule);
        $this->getAuthManager()->invalidateCache();

        Yii::$app->getSession()->setFlash('success', Yii::t('usuario', 'Authorization rule has been removed.'));
        return $this->redirect(['index']);
    }

    /**
     * @param $name
     *
     * @throws NotFoundHttpException
     * @return mixed|null|\yii\rbac\Rule
     */
    protected function findRule($name)
    {
        $rule = $this->getAuthManager()->getRule($name);

        if (!($rule instanceof \yii\rbac\Rule)) {
            throw new NotFoundHttpException(Yii::t('usuario', 'Rule {0} not found.', $name));
        }

        return $rule;
    }
}
