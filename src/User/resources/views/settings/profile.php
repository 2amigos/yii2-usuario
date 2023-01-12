<?php

/*
 * This file is part of the 2amigos/yii2-usuario project.
 *
 * (c) 2amigOS! <http://2amigos.us/>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

use Da\User\Helper\TimezoneHelper;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;

/**
 * @var yii\web\View           $this
 * @var yii\widgets\ActiveForm $form
 * @var \Da\User\Model\Profile $model
 * @var TimezoneHelper         $timezoneHelper
 */

$this->title = Yii::t('usuario', 'Profile settings');
$this->params['breadcrumbs'][] = $this->title;
$timezoneHelper = $model->make(TimezoneHelper::class);
?>

<div class="clearfix"></div>

<?= $this->render('/shared/_alert', ['module' => Yii::$app->getModule('user')]) ?>

<div class="row">
    <div class="col-md-3">
        <?= $this->render('_menu') ?>
    </div>
    <div class="col-md-9">
        <div class="card">
            <div class="card-header">
                <h3 class="m-0"><?= Html::encode($this->title) ?></h3>
            </div>
            <div class="card-body">
                <?php $form = ActiveForm::begin(
                    [
                        'id' => $model->formName(),
                        'layout' => 'horizontal',
                        'enableAjaxValidation' => true,
                        'enableClientValidation' => false,
                        'validateOnBlur' => false,
                    ]
                ); ?>

                <?= $form->field($model, 'name') ?>

                <?= $form->field($model, 'public_email') ?>

                <?= $form->field($model, 'website') ?>

                <?= $form->field($model, 'location') ?>

                <?= $form
                    ->field($model, 'timezone')
                    ->dropDownList(ArrayHelper::map($timezoneHelper->getAll(), 'timezone', 'name'));
                ?>
                <?= $form
                    ->field($model, 'gravatar_email')
                    ->hint(
                        Html::a(
                            Yii::t('usuario', 'Change your avatar at Gravatar.com'),
                            'https://gravatar.com',
                            ['target' => '_blank']
                        )
                    ) ?>

                <?= $form->field($model, 'bio')->textarea() ?>

                <div class="form-group">
                    <div class="offset-sm-2 col-lg-10">
                        <div class="d-grid">
                            <?= Html::submitButton(
                                Yii::t('usuario', 'Save'),
                                ['class' => 'btn btn-success']
                            ) ?>
                        </div>
                        <br>
                    </div>
                </div>

                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>
