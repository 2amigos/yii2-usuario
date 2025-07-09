<?php

use Da\User\Module;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Json;
use Da\User\resources\assets\PasskeyAsset;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var Module         $module */

$module = Yii::$app->getModule('user');



$this->title = 'Create a new passkey';

$this->registerJsVar('userId', Json::htmlEncode((string)Yii::$app->user->id));
$this->registerJsVar('username', Json::htmlEncode(Yii::$app->user->identity->username));
$this->registerJsVar('homeUrl', Url::home(true));

$count = \Da\User\Model\UserEntity::find()
    ->where(['user_id' => Yii::$app->user->id])
    ->count();
$this->registerJsVar('numberOfPasskeys', $count+1);


PasskeyAsset::register($this);
$homepageUrl = \yii\helpers\Url::current();
$this->registerJs(<<<JS
$('#start-passkey-btn').click(function(e) {
    e.preventDefault();

    var form = $('#passkey-form');
    form.yiiActiveForm('validate');

    setTimeout(function() {
        var hasErrors = form.find(".has-error").length > 0;

        if (!hasErrors) {
            $(this).registerWithPasskey();
        }
    }.bind(this), 100);
});

$('#passkey-form').on('keydown', function(e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        $('#start-passkey-btn').trigger('click');
    }
});
JS);

?>

<h1><?= Html::encode($this->title) ?></h1>

<?php
if($count == $module->maxPasskeysForUser){
    ?> <p>Sorry, you are allowed to have a maximum of <?= Html::encode($module->maxPasskeysForUser) ?> passkeys.</p> <?php
}else{
?>
<div class="user-entity-form">

    <?php $form = ActiveForm::begin([
        'id' => 'passkey-form',
        'action' => ['/user/user-entity/store-passkey'],
        'method' => 'post',
    ]); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true])->label('Name for passkey') ?>

    <?= Html::activeHiddenInput($model, 'id', ['id' => 'uuid_id']) ?>
    <?= Html::activeHiddenInput($model, 'credential_id', ['id' => 'credential_id']) ?>
    <?= Html::activeHiddenInput($model, 'public_key', ['id' => 'public_key']) ?>
    <?= Html::activeHiddenInput($model, 'sign_count', ['id' => 'sign_count']) ?>
    <?= Html::activeHiddenInput($model, 'attestation_format', ['id' => 'attestation_format']) ?>
    <?= Html::activeHiddenInput($model, 'device_id', ['id' => 'device_id']) ?>

    <div class="form-group">
        <?= Html::submitButton('Register Passkey', ['class' => 'btn btn-success', 'style' => 'display:none;', 'id' => 'submit-button']) ?>
        <button type="button" class="btn btn-primary" id="start-passkey-btn">Register Passkey</button>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php }
?>


