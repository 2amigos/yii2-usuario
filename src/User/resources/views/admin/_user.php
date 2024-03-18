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
use kartik\typeahead\Typeahead;
use yii\helpers\Html;
use yii\helpers\Url;

$source = Yii::$app->request->get('source') ?: $user->source;

$emailInputId = Html::getInputId($user, 'email');
$sourceId = Html::getInputId($user, 'source');
$this->registerJs(<<<JS
    function updateFromLdap(event, data) {
          $("#$emailInputId").val(data.value).change();
    }
    $('#$sourceId').change(function() {
        var source = $(this).val();
        $.pjax.reload({container: '#pjax-user-create', data: {source: source}})
    })
JS);

if ($user->isNewRecord) {
    if (Yii::$app->getModule('user')->searchUsersInLdap && $source == UserSourceType::LDAP) {
        echo $form->field($user, 'source')->dropDownList(UserSourceType::all(), ['value' => $source]);
        echo $form->field($user, 'email')->widget(Typeahead::class, [
            'options' => ['placeholder' => Yii::t('usuario', 'Filter as you type...'), 'autocomplete' => 'off'],
            'pluginOptions' => ['highlight' => true],
            'dataset' => [
                [
                    'display' => 'value',
                    'remote' => [
                        'url' => Url::to(['/usuario-ldap/ldap/search']) . '?q=%QUERY', // You can add &limit to set a results limit, 20 to default
                        'wildcard' => '%QUERY'
                    ]
                ]
            ],
            // When the email is selected, get the username and change the source from local to ldap
            'pluginEvents' => [
                'typeahead:select' => 'updateFromLdap',
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
