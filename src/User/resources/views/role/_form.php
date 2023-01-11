<?php

/*
 * This file is part of the 2amigos/yii2-usuario project.
 *
 * (c) 2amigOS! <http://2amigos.us/>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

/**
 * @var yii\web\View $this
 * @var \Da\User\Model\Role $model
 */

use Da\User\Helper\AuthHelper;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;

$unassignedItems = Yii::$container->get(AuthHelper::class)->getUnassignedItems($model);
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
        'prompt' => 'Select rule...'
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
