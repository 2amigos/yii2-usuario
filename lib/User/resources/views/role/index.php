<?php
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\Url;

/**
 * @var $dataProvider array
 * @var $searchModel  \Da\User\Search\RoleSearch
 * @var $this         yii\web\View
 */


$this->title = Yii::t('user', 'Roles');
$this->params['breadcrumbs'][] = $this->title;

?>

<?php $this->beginContent('@Da/User/resources/views/shared/admin_layout.php') ?>

<?= GridView::widget(
    [
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'layout' => "{items}\n{pager}",
        'columns' => [
            [
                'attribute' => 'name',
                'header' => Yii::t('user', 'Name'),
                'options' => [
                    'style' => 'width: 20%'
                ],
            ],
            [
                'attribute' => 'description',
                'header' => Yii::t('user', 'Description'),
                'options' => [
                    'style' => 'width: 55%'
                ],
            ],
            [
                'attribute' => 'rule_name',
                'header' => Yii::t('user', 'Rule name'),
                'options' => [
                    'style' => 'width: 20%'
                ],
            ],
            [
                'class' => ActionColumn::className(),
                'template' => '{update} {delete}',
                'urlCreator' => function ($action, $model) {
                    return Url::to(['/user/role/' . $action, 'name' => $model['name']]);
                },
                'options' => [
                    'style' => 'width: 5%'
                ],
            ]
        ],
    ]
) ?>

<?php $this->endContent() ?>
