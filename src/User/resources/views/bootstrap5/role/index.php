<?php

/*
 * This file is part of the 2amigos/yii2-usuario project.
 *
 * (c) 2amigOS! <http://2amigos.us/>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\Url;

/**
 * @var \yii\data\DataProviderInterface $dataProvider
 * @var \Da\User\Search\RoleSearch $searchModel
 * @var yii\web\View $this
 * @var \Da\User\Module $module
 */

$this->title = Yii::t('usuario', 'Roles');
$this->params['breadcrumbs'][] = $this->title;

?>

<?php $this->beginContent($module->viewPath . '/shared/admin_layout.php') ?>
<div class="table-responsive">
<?= GridView::widget(
    [
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'layout' => "{items}\n{pager}",
        'columns' => [
            [
                'attribute' => 'name',
                'header' => Yii::t('usuario', 'Name'),
                'options' => [
                    'style' => 'width: 20%',
                ],
            ],
            [
                'attribute' => 'description',
                'header' => Yii::t('usuario', 'Description'),
                'options' => [
                    'style' => 'width: 55%',
                ],
            ],
            [
                'attribute' => 'rule_name',
                'header' => Yii::t('usuario', 'Rule name'),
                'options' => [
                    'style' => 'width: 20%',
                ],
            ],
            [
                'class' => ActionColumn::class,
                'template' => '{update} {delete}',
                'urlCreator' => function ($action, $model) {
                    return Url::to(['/user/role/' . $action, 'name' => $model['name']]);
                },
                'options' => [
                    'style' => 'width: 5%',
                ],
            ],
        ],
    ]
) ?>
</div>
<?php $this->endContent() ?>
