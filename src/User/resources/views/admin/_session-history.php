<?php

/*
 * This file is part of the 2amigos/yii2-usuario project.
 *
 * (c) 2amigOS! <http://2amigos.us/>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

use Da\User\Widget\SessionStatusWidget;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use Da\User\Model\SessionHistory;
use Da\User\Search\SessionHistorySearch;
use yii\web\View;
use yii\data\ActiveDataProvider;

/**
 * @var View $this
 * @var SessionHistorySearch $searchModel
 * @var ActiveDataProvider $dataProvider
 * @var \Da\User\Model\User $user
 * @var \Da\User\Module $module
 */
?>

<?php $this->beginContent($module->viewPath. '/admin/update.php', ['user' => $user]) ?>
    <div class="row">
        <div class="col-xs-12">
            <?= Html::a(
                Yii::t('usuario', 'Terminate all sessions'),
                ['/user/admin/terminate-sessions', 'id' => $user->id],
                [
                    'class' => 'btn btn-danger btn-xs pull-right',
                    'data-method' => 'post'
                ]
            ) ?>
        </div>
    </div>
    <hr>

<?php Pjax::begin(); ?>

<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns' => [
        'user_agent',
        'ip',
        [
            'contentOptions' => [
                'class' => 'text-nowrap',
            ],
            'label' => Yii::t('usuario', 'Status'),
            'value' => function (SessionHistory $model) {
                return SessionStatusWidget::widget(['model' => $model]);
            },
        ],
        [
            'attribute' => 'updated_at',
            'format' => 'datetime'
        ],
    ],
]); ?>
<?php Pjax::end(); ?>

<?php $this->endContent() ?>
