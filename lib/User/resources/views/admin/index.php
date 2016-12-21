<?php

/*
 * This file is part of the 2amigos/yii2-usuario project.
 *
 * (c) 2amigOS! <http://2amigos.us/>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

use yii\grid\GridView;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\Pjax;

/**
 * @var $this         yii\web\View
 * @var $dataProvider yii\data\ActiveDataProvider
 * @var $searchModel  Da\User\Search\UserSearch
 */

$this->title = Yii::t('user', 'Manage users');
$this->params['breadcrumbs'][] = $this->title;
?>

<?php $this->beginContent('@Da/User/resources/views/shared/admin_layout.php') ?>

<?php Pjax::begin() ?>

<?= GridView::widget(
    [
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'layout' => "{items}\n{pager}",
        'columns' => [
            'username',
            'email:email',
            [
                'attribute' => 'registration_ip',
                'value' => function ($model) {
                    return $model->registration_ip == null
                        ? '<span class="not-set">'.Yii::t('user', '(not set)').'</span>'
                        : $model->registration_ip;
                },
                'format' => 'html',
            ],
            [
                'attribute' => 'created_at',
                'value' => function ($model) {
                    if (extension_loaded('intl')) {
                        return Yii::t('user', '{0, date, MMMM dd, YYYY HH:mm}', [$model->created_at]);
                    } else {
                        return date('Y-m-d G:i:s', $model->created_at);
                    }
                },
            ],
            [
                'header' => Yii::t('user', 'Confirmation'),
                'value' => function ($model) {
                    if ($model->isConfirmed) {
                        return '<div class="text-center">
                                <span class="text-success">' .Yii::t('user', 'Confirmed').'</span>
                            </div>';
                    } else {
                        return Html::a(
                            Yii::t('user', 'Confirm'),
                            ['confirm', 'id' => $model->id],
                            [
                                'class' => 'btn btn-xs btn-success btn-block',
                                'data-method' => 'post',
                                'data-confirm' => Yii::t('user', 'Are you sure you want to confirm this user?'),
                            ]
                        );
                    }
                },
                'format' => 'raw',
                'visible' => Yii::$app->getModule('user')->enableEmailConfirmation,
            ],
            [
                'header' => Yii::t('user', 'Block status'),
                'value' => function ($model) {
                    if ($model->isBlocked) {
                        return Html::a(
                            Yii::t('user', 'Unblock'),
                            ['block', 'id' => $model->id],
                            [
                                'class' => 'btn btn-xs btn-success btn-block',
                                'data-method' => 'post',
                                'data-confirm' => Yii::t('user', 'Are you sure you want to unblock this user?'),
                            ]
                        );
                    } else {
                        return Html::a(
                            Yii::t('user', 'Block'),
                            ['block', 'id' => $model->id],
                            [
                                'class' => 'btn btn-xs btn-danger btn-block',
                                'data-method' => 'post',
                                'data-confirm' => Yii::t('user', 'Are you sure you want to block this user?'),
                            ]
                        );
                    }
                },
                'format' => 'raw',
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{update} {delete}',
            ],
        ],
    ]
); ?>

<?php Pjax::end() ?>

<?php $this->endContent() ?>
