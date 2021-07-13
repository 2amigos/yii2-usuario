<?php

use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\rbac\Rule;

/**
 * @var $dataProvider \yii\data\ActiveDataProvider
 * @var $searchModel  \Da\User\Search\RuleSearch
 * @var $this         yii\web\View
 */

$this->title = Yii::t('usuario', 'Rules');
$this->params['breadcrumbs'][] = $this->title;

?>

<?php $this->beginContent('@Da/User/resources/views/shared/admin_layout.php') ?>
<div class="table-responsive">
<?= GridView::widget(
    [
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'layout' => "{items}\n{pager}",
        'columns' => [
            [
                'attribute' => 'name',
                'label' => Yii::t('usuario', 'Name'),
                'options' => [
                    'style' => 'width: 20%'
                ],
            ],
            [
                'attribute' => 'className',
                'label' => Yii::t('usuario', 'Class'),
                'value' => function ($row) {
                    if (!isset($row['data']) || ($data = @unserialize(is_resource($row['data']) ? stream_get_contents($row['data']) : $row['data'])) === false) {
                        $data = null;
                    }

                    return get_class($data);
                },
                'options' => [
                    'style' => 'width: 20%'
                ],
            ],
            [
                'attribute' => 'created_at',
                'label' => Yii::t('usuario', 'Created at'),
                'format' => 'datetime',
                'options' => [
                    'style' => 'width: 20%'
                ],
            ],
            [
                'attribute' => 'updated_at',
                'label' => Yii::t('usuario', 'Updated at'),
                'format' => 'datetime',
                'options' => [
                    'style' => 'width: 20%'
                ],
            ],
            [
                'class' => ActionColumn::class,
                'template' => '{update} {delete}',
                'urlCreator' => function ($action, $model) {
                    return Url::to(['/user/rule/' . $action, 'name' => $model['name']]);
                },
                'options' => [
                    'style' => 'width: 5%'
                ],
            ]
        ],
    ]
) ?>
</div>

<?php $this->endContent() ?>
