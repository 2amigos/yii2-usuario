<?php

use Da\User\Module;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;
use yii\helpers\Json;
use Da\User\resources\assets\PasskeyAsset;
use yii\helpers\Url;
use CBOR\Decoder;


/** @var yii\web\View $this */
/** @var Module         $module */
/** @var Da\User\Model\UserEntity $model */




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
$('#passkey-form').bindPassKeyCreationSubmit();
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

    <?=$form->field($model, 'name')->textInput(['maxlength' => true])->label('Name for passkey') ?>
    <?= Html::activeHiddenInput($model, 'id', ['id' => 'uuid_id']) ?>
    <?= Html::activeHiddenInput($model, 'credential_id', ['id' => 'credential_id']) ?>
    <?= Html::activeHiddenInput($model, 'public_key', ['id' => 'public_key']) ?>
    <?= Html::activeHiddenInput($model, 'attestation_format', ['id' => 'attestation_format']) ?>
    <?= Html::activeHiddenInput($model, 'device_id', ['id' => 'device_id']) ?>

    <div class="form-group">
        <?= Html::submitButton('Register Passkey', ['class' => 'btn btn-success', 'id' => 'submit-button']) ?>
    </div>


    <?php ActiveForm::end();?>

</div>

<?php }
?>


<?php
/*
 *
 * TODO CBOR LATO CLIENT E SERVER
 *
 * function base64UrlDecode(string $data): string {
    $remainder = strlen($data) % 4;
    if ($remainder) {
        $data .= str_repeat('=', 4 - $remainder);
    }
    return base64_decode(strtr($data, '-_', '+/'));
}

function getAttestationFormat(string $attestationObjectBase64Url): string {
    $attestationObject = base64UrlDecode($attestationObjectBase64Url);

    $decoder = new Decoder();
    $decodedCbor = $decoder->decode($attestationObject);

    $fmt = $decodedCbor->getNormalizedData()['fmt'] ?? null;

    if (!$fmt) {
        throw new Exception("Campo 'fmt' mancante nell'attestationObject.");
    }

    return $fmt;
}*/




