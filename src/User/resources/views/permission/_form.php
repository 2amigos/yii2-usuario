<?php

/*
 * This file is part of the 2amigos/yii2-usuario project.
 *
 * (c) 2amigOS! <http://2amigos.us/>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;
use kartik\select2\Select2;

/**
 * @var yii\web\View $this
 * @var Da\User\Model\Permission $model
 * @var string[] $unassignedItems
 */

?>

<?php $form = ActiveForm::begin(
    [
        'enableClientValidation' => false,
        'enableAjaxValidation' => true,
    ]
) ?>

<?= $form->field($model, 'name') ?>

<?= $form->field($model, 'description') ?>

<?= $form->field($model, 'rule')->widget(Select2::class, [
    'data' => ArrayHelper::map(Yii::$app->getAuthManager()->getRules(), 'name', 'name'),
    'options' => [
        'prompt' => Yii::t('usuario', 'Select rule...'),
    ]
]) ?>


<?= $form->field($model, 'children')->widget(
    Select2::class,
    [
        'data' => $unassignedItems,
        'options' => [
            'id' => 'children',
            'multiple' => true,
        ],
    ]
) ?>

<?= Html::submitButton(Yii::t('usuario', 'Save'), ['class' => 'btn btn-success btn-block']) ?>

<?php ActiveForm::end() ?>
