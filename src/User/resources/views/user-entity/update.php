<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var Da\User\Model\UserEntity $model */

$this->title = Yii::t('usuario','Update Passkey: ') . Html::encode($model->name);
?>

<h1><?= Html::encode($this->title) ?></h1>

<div class="passkey-update-view">
    <?php if (Yii::$app->session->hasFlash('success')): ?>
        <div class="alert alert-success"><?= Yii::$app->session->getFlash('success') ?></div>
    <?php endif; ?>

    <?php if (Yii::$app->session->hasFlash('error')): ?>
        <div class="alert alert-danger"><?= Yii::$app->session->getFlash('error') ?></div>
    <?php endif; ?>

    <table class="table table-bordered">
        <tbody>
        <tr><th>Device ID</th><td><?= Html::encode($model->device_id) ?></td></tr>
        <tr><th>Sign Count</th><td><?= Html::encode($model->sign_count) ?></td></tr>
        <tr><th>Last Used At</th><td><?= $model->last_used_at ?: '-' ?></td></tr>
        <tr><th>Created At</th><td><?= $model->created_at ?></td></tr>
        </tbody>
    </table>

    <?php $form = ActiveForm::begin(); ?>
    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
    <div class="form-group">
        <?= Html::submitButton(Yii::t('usuario','Save Changes') , ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('usuario','Cancel') , ['index-passkey'], ['class' => 'btn btn-secondary']) ?>

    </div>
    <?php ActiveForm::end(); ?>

</div>
