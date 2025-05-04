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
 * @var yii\widgets\ActiveForm $form
 * @var \Da\User\Model\User $user
 */

use Da\User\Dictionary\UserSourceType;
use dosamigos\selectize\SelectizeTextInput;
use yii\helpers\Html;
use yii\helpers\Url;

$source = Yii::$app->request->get('source') ?: $user->source;
$ldapUidId = Html::getInputId($user, 'ldapUid');
$sourceId = Html::getInputId($user, 'source');
$this->registerJs(<<<JS
    function updateFromLdap(data) {
        $("#$ldapUidId").val(data).change();
    }
    $('#$sourceId').change(function() {
        var source = $(this).val();
        $.pjax.reload({container: '#pjax-user-create', data: {source: source}})
    })
JS);

if ($user->isNewRecord) {
    if (Yii::$app->getModule('user')->searchUsersInLdap && $source == UserSourceType::LDAP) {
        echo $form->field($user, 'source')->dropDownList(UserSourceType::all(), ['value' => $source]);
        echo $form->field($user, 'ldapUid')->widget(SelectizeTextInput::class, [
            'loadUrl' => Url::to(['/usuario-ldap/ldap/search']),
            'queryParam' => 'q',
            'options' => [
                'placeholder' => Yii::t('usuario', 'Filter as you type...'),
                'autocomplete' => 'off',
            ],
            'clientOptions' => [
                'valueField' => 'value',
                'labelField' => 'label',
                'searchField' => ['value', 'label', 'q'],
                'create' => false,
                'maxItems' => 1,
                'onChange' => new \yii\web\JsExpression("
                    function(value) {
                    console.log(value);
                        updateFromLdap(value);
                    }
                "),
            ],
        ]);
    } else {
        echo $form->field($user, 'source')->dropDownList(UserSourceType::all(), ['value' => $source]);
        echo $form->field($user, 'email')->textInput(['maxlength' => 255]);
        echo $form->field($user, 'username')->textInput(['maxlength' => 255]);
        echo $form->field($user, 'password')->passwordInput();
    }
} else {
    echo $form->field($user, 'email')->textInput(['maxlength' => 255]);
    echo $form->field($user, 'username')->textInput(['maxlength' => 255]);
    echo $form->field($user, 'password')->passwordInput();
}
