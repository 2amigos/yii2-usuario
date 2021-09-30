<?php

/*
 * This file is part of the 2amigos/yii2-usuario project.
 *
 * (c) 2amigOS! <http://2amigos.us/>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

use yii\bootstrap4\ActiveForm;
use yii\helpers\Html;

/* @var yii\web\View $this */
/* @var Da\User\Model\User $user */

?>

<?php $this->beginContent('@Da/User/resources/views/admin/update.php', ['user' => $user]) ?>

<?php $form = ActiveForm::begin(
    [
        'layout' => 'horizontal',
        'enableAjaxValidation' => true,
        'enableClientValidation' => false,
        'fieldConfig' => [
            'horizontalCssClasses' => [
                'wrapper' => 'col-sm-9',
            ],
        ],
    ]
); ?>

<?= $this->render('/admin/_user', ['form' => $form, 'user' => $user]) ?>

<div class="form-group">
    <div class="col-lg-offset-3 col-lg-9">
        <?= Html::submitButton(Yii::t('usuario', 'Update'), ['class' => 'btn btn-block btn-success']) ?>
    </div>
</div>

<?php ActiveForm::end(); ?>

<?php $this->endContent() ?>
