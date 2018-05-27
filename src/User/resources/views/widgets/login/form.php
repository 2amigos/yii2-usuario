<?php

/*
 * This file is part of the 2amigos/yii2-usuario project.
 *
 * (c) 2amigOS! <http://2amigos.us/>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/**
 * @var yii\web\View           $this
 * @var yii\widgets\ActiveForm $form
 * @var Da\User\Form\LoginForm $model
 * @var string                 $action
 */

?>

<?php if (Yii::$app->user->isGuest): ?>
    <?php $form = ActiveForm::begin(
        [
            'id' => 'login-widget-form',
            'action' => Url::to(['/user/security/login']),
            'enableAjaxValidation' => true,
            'enableClientValidation' => false,
            'validateOnBlur' => false,
            'validateOnType' => false,
            'validateOnChange' => false,
        ]
    ) ?>

    <?= $form->field($model, 'login')->textInput(['placeholder' => 'Login']) ?>

    <?= $form->field($model, 'password')->passwordInput(['placeholder' => 'Password']) ?>

    <?= $form->field($model, 'rememberMe')->checkbox() ?>

    <?= Html::submitButton(Yii::t('usuario', 'Sign in'), ['class' => 'btn btn-primary btn-block']) ?>

    <?php ActiveForm::end(); ?>
<?php else: ?>
    <?= Html::a(
        Yii::t('usuario', 'Logout'),
        ['/user/security/logout'],
        [
            'class' => 'btn btn-danger btn-block',
            'data-method' => 'post',
        ]
    ) ?>
<?php endif ?>
