<?php

/*
 * This file is part of the 2amigos/yii2-usuario project.
 *
 * (c) 2amigOS! <http://2amigos.us/>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

use Da\User\Widget\ConnectWidget;
use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;

/**
 * @var yii\web\View            $this
 * @var \Da\User\Form\LoginForm $model
 * @var \Da\User\Module         $module
 */

$this->title = Yii::t('usuario', 'Sign in');
$this->params['breadcrumbs'][] = $this->title;
?>

<?= $this->render('/shared/_alert', ['module' => Yii::$app->getModule('user')]) ?>

<div class="row align-items-center">
    <div class="col"></div>
    <div class="col-xs-8 col-sm-7 col-md-6 col-lg-5 col-xl-4 ">
        <div class="card">
            <div class="card-header">
                <h3 class="m-0"><?= Html::encode($this->title) ?></h3>
            </div>
            <div class="card-body">
                <?php $form = ActiveForm::begin(
                    [
                        'id' => $model->formName(),
                        'enableAjaxValidation' => true,
                        'enableClientValidation' => false,
                        'validateOnBlur' => false,
                        'validateOnType' => false,
                        'validateOnChange' => false,
                    ]
                ) ?>
                <?= $form->field(
                    $model,
                    'twoFactorAuthenticationCode',
                    ['inputOptions' => ['autofocus' => 'autofocus', 'class' => 'form-control', 'tabindex' => '1']]
                ) ?>
                <div class="row">
                    <div class="col-md-6">
                        <?= Html::a(
                            Yii::t('usuario', 'Cancel'),
                            ['login'],
                            ['class' => 'btn btn-default btn-block', 'tabindex' => '3']
                        ) ?>
                    </div>
                    <div class="col-md-6">
                        <?= Html::submitButton(
                            Yii::t('usuario', 'Confirm'),
                            ['class' => 'btn btn-primary btn-block', 'tabindex' => '3']
                        ) ?>
                    </div>
                </div>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
    <div class="col"></div>

</div>
