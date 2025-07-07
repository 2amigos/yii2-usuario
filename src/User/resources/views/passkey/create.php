<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Json;

/** @var yii\web\View $this */
/** @var Da\User\Model\UserEntity $model */

$this->title = 'Create a new passkey';

$this->registerJsVar('userId', Json::htmlEncode((string)Yii::$app->user->id));
$this->registerJsVar('username', Json::htmlEncode(Yii::$app->user->identity->username));
?>

<h1><?= Html::encode($this->title) ?></h1>

<div class="user-entity-form">

    <?php $form = ActiveForm::begin([
        'id' => 'passkey-form',
        'action' => ['user-entity/store-passkey'],
        'method' => 'post',
    ]); ?>

    <div><p><b>TIP:</b> Save the passkey with the same name on this site and on the passkey provider to make the organization of your passkeys easier!</p></div>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true])->label('Name for passkey') ?>

    <?= Html::activeHiddenInput($model, 'id', ['id' => 'uuid_id']) ?>
    <?= Html::activeHiddenInput($model, 'credential_id', ['id' => 'credential_id']) ?>
    <?= Html::activeHiddenInput($model, 'public_key', ['id' => 'public_key']) ?>
    <?= Html::activeHiddenInput($model, 'sign_count', ['id' => 'sign_count']) ?>
    <?= Html::activeHiddenInput($model, 'attestation_format', ['id' => 'attestation_format']) ?>
    <?= Html::activeHiddenInput($model, 'device_id', ['id' => 'device_id']) ?>

    <div class="form-group">
        <?= Html::submitButton('Register Passkey', ['class' => 'btn btn-success', 'style' => 'display:none;', 'id' => 'submit-button']) ?>
        <button type="button" class="btn btn-primary" id="start-passkey-btn">Start Passkey Registration</button>
    </div>

    <?php ActiveForm::end(); ?>

</div>


