<?php

use Da\User\Model\User;
use yii\bootstrap\Nav;
use yii\web\View;
use yii\helpers\Html;

/*
 * @var View $this
 * @var User $user
 * @var string $content
 */

$this->title = Yii::t('user', 'Update user account');
$this->params['breadcrumbs'][] = ['label' => Yii::t('user', 'Users'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="clearfix"></div>
<?= $this->render(
    '/shared/_alert',
    [
        'module' => Yii::$app->getModule('user'),
    ]
) ?>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><?= Html::encode($this->title) ?></h3>
            </div>
            <div class="panel-body">
                <?= $this->render('/shared/_menu') ?>
                <div class="row">
                    <div class="col-md-3">
                        <div class="panel panel-default">
                            <div class="panel-body">
                                <?= Nav::widget(
                                    [
                                        'options' => [
                                            'class' => 'nav-pills nav-stacked',
                                        ],
                                        'items' => [
                                            [
                                                'label' => Yii::t('user', 'Account details'),
                                                'url' => ['/user/admin/update', 'id' => $user->id],
                                            ],
                                            [
                                                'label' => Yii::t('user', 'Profile details'),
                                                'url' => ['/user/admin/update-profile', 'id' => $user->id],
                                            ],
                                            [
                                                'label' => Yii::t('user', 'Information'),
                                                'url' => ['/user/admin/info', 'id' => $user->id],
                                            ],
                                            [
                                                'label' => Yii::t('user', 'Assignments'),
                                                'url' => ['/user/admin/assignments', 'id' => $user->id],
                                            ],
                                            '<hr>',
                                            [
                                                'label' => Yii::t('user', 'Confirm'),
                                                'url' => ['/user/admin/confirm', 'id' => $user->id],
                                                'visible' => !$user->isConfirmed,
                                                'linkOptions' => [
                                                    'class' => 'text-success',
                                                    'data-method' => 'post',
                                                    'data-confirm' => Yii::t(
                                                        'user',
                                                        'Are you sure you want to confirm this user?'
                                                    ),
                                                ],
                                            ],
                                            [
                                                'label' => Yii::t('user', 'Block'),
                                                'url' => ['/user/admin/block', 'id' => $user->id],
                                                'visible' => !$user->isBlocked,
                                                'linkOptions' => [
                                                    'class' => 'text-danger',
                                                    'data-method' => 'post',
                                                    'data-confirm' => Yii::t(
                                                        'user',
                                                        'Are you sure you want to block this user?'
                                                    ),
                                                ],
                                            ],
                                            [
                                                'label' => Yii::t('user', 'Unblock'),
                                                'url' => ['/user/admin/block', 'id' => $user->id],
                                                'visible' => $user->isBlocked,
                                                'linkOptions' => [
                                                    'class' => 'text-success',
                                                    'data-method' => 'post',
                                                    'data-confirm' => Yii::t(
                                                        'user',
                                                        'Are you sure you want to unblock this user?'
                                                    ),
                                                ],
                                            ],
                                            [
                                                'label' => Yii::t('user', 'Delete'),
                                                'url' => ['/user/admin/delete', 'id' => $user->id],
                                                'linkOptions' => [
                                                    'class' => 'text-danger',
                                                    'data-method' => 'post',
                                                    'data-confirm' => Yii::t(
                                                        'user',
                                                        'Are you sure you want to delete this user?'
                                                    ),
                                                ],
                                            ],
                                        ],
                                    ]
                                ) ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-9">
                        <div class="panel panel-default">
                            <div class="panel-body">
                                <?= $content ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
