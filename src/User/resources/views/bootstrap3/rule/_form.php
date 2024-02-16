<?php

/**
 * @var yii\web\View $this
 * @var \Da\User\Model\Rule $model
 */

use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>


<?php $form = ActiveForm::begin(
    [
        'enableClientValidation' => false,
        'enableAjaxValidation' => true,
    ]
) ?>

<?= $form->field($model, 'name') ?>

<?= $form->field($model, 'className') ?>

<?= Html::submitButton(Yii::t('usuario', 'Save'), ['class' => 'btn btn-success btn-block']) ?>

<?php ActiveForm::end() ?>
