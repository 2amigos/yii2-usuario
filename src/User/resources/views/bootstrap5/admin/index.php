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
use yii\widgets\Pjax;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var Da\User\Search\UserSearch $searchModel
 * @var Da\User\Module $module
 */

$this->title = Yii::t('usuario', 'Manage users');
$this->params['breadcrumbs'][] = $this->title;
\yii\bootstrap5\BootstrapIconAsset::register($this);
?>

<?php $this->beginContent($module->viewPath . '/shared/admin_layout.php') ?>

<?php Pjax::begin() ?>
<div class="table-responsive">
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
                        ? '<span class="not-set">' . Yii::t('usuario', '(not set)') . '</span>'
                        : $model->registration_ip;
                },
                'format' => 'html',
                'visible' => !$module->disableIpLogging,
            ],
            [
                'attribute' => 'created_at',
                'value' => function ($model) {
                    if (extension_loaded('intl')) {
                        return Yii::t('usuario', '{0, date, MMM dd, YYYY HH:mm}', [$model->created_at]);
                    }

                    return date('Y-m-d G:i:s', $model->created_at);
                },
            ],
            [
                'attribute' => 'last_login_at',
                'value' => function ($model) {
                    if (!$model->last_login_at || $model->last_login_at == 0) {
                        return Yii::t('usuario', 'Never');
                    } elseif (extension_loaded('intl')) {
                        return Yii::t('usuario', '{0, date, MMM dd, YYYY HH:mm}', [$model->last_login_at]);
                    } else {
                        return date('Y-m-d G:i:s', $model->last_login_at);
                    }
                },
            ],
            [
                'attribute' => 'last_login_ip',
                'value' => function ($model) {
                    return $model->last_login_ip == null
                        ? '<span class="not-set">' . Yii::t('usuario', '(not set)') . '</span>'
                        : $model->last_login_ip;
                },
                'format' => 'html',
                'visible' => !$module->disableIpLogging,
            ],
            [
                'header' => Yii::t('usuario', 'Confirmation'),
                'value' => function ($model) {
                    if ($model->isConfirmed) {
                        return '<div class="text-center">
                                <span class="text-success">' . Yii::t('usuario', 'Confirmed') . '</span>
                            </div>';
                    }

                    return Html::a(
                        Yii::t('usuario', 'Confirm'),
                        ['confirm', 'id' => $model->id],
                        [
                            'class' => 'btn btn-xs btn-success btn-block',
                            'data-method' => 'post',
                            'data-confirm' => Yii::t('usuario', 'Are you sure you want to confirm this user?'),
                        ]
                    );
                },
                'format' => 'raw',
                'visible' => $module->enableEmailConfirmation,
            ],
            'password_age',
            [
                'header' => Yii::t('usuario', 'Block status'),
                'value' => function ($model) {
                    if ($model->isBlocked) {
                        return Html::a(
                            Yii::t('usuario', 'Unblock'),
                            ['block', 'id' => $model->id],
                            [
                                'class' => 'btn btn-xs btn-success btn-block',
                                'data-method' => 'post',
                                'data-confirm' => Yii::t('usuario', 'Are you sure you want to unblock this user?'),
                            ]
                        );
                    }

                    return Html::a(
                        Yii::t('usuario', 'Block'),
                        ['block', 'id' => $model->id],
                        [
                            'class' => 'btn btn-xs btn-danger btn-block',
                            'data-method' => 'post',
                            'data-confirm' => Yii::t('usuario', 'Are you sure you want to block this user?'),
                        ]
                    );
                },
                'format' => 'raw',
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{switch} {reset} {force-password-change} {update} {delete}',
                'buttons' => [
                    'switch' => function ($url, $model) use ($module) {
                        if ($model->id != Yii::$app->user->id && $module->enableSwitchIdentities) {
                            return Html::a(
                                '<i class="bi-person-fill"></i>',
                                ['/user/admin/switch-identity', 'id' => $model->id],
                                [
                                    'title' => Yii::t('usuario', 'Impersonate this user'),
                                    'data-confirm' => Yii::t(
                                        'usuario',
                                        'Are you sure you want to switch to this user for the rest of this Session?'
                                    ),
                                    'data-method' => 'POST',
                                ]
                            );
                        }

                        return null;
                    },
                    'reset' => function ($url, $model) use ($module) {
                        if($module->allowAdminPasswordRecovery) {
                            return Html::a(
                                '<i class="bi-lightning-charge-fill"></i>',
                                ['/user/admin/password-reset', 'id' => $model->id],
                                [
                                    'title' => Yii::t('usuario', 'Send password recovery email'),
                                    'data-confirm' => Yii::t(
                                        'usuario',
                                        'Are you sure you wish to send a password recovery email to this user?'
                                    ),
                                    'data-method' => 'POST',
                                ]
                            );
                        }

                        return null;
                    },
                    'force-password-change' => function ($url, $model) use ($module) {
                        if (is_null($module->maxPasswordAge)) {
                            return null;
                        }
                        return Html::a(
                            '<i class="fas fa-stopwatch"></i>',
                            ['/user/admin/force-password-change', 'id' => $model->id],
                            [
                                'title' => Yii::t('usuario', 'Force password change at next login'),
                                'data-confirm' => Yii::t(
                                    'usuario',
                                    'Are you sure you wish the user to change their password at next login?'
                                ),
                                'data-method' => 'POST',
                            ]
                        );
                    },
                ]
            ],
        ],
    ]
); ?>
</div>
<?php Pjax::end() ?>

<?php $this->endContent() ?>