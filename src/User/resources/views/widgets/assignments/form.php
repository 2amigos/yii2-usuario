<?php

/*
 * This file is part of the 2amigos/yii2-usuario project.
 *
 * (c) 2amigOS! <http://2amigos.us/>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

use yii\bootstrap5\Alert;
use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;
use kartik\select2\Select2;

/** @var \yii\web\View $this */
/** @var string[] $availableItems */
/** @var Da\User\Model\Assignment $model */


?>

<?php if ($model->updated): ?>

    <?= Alert::widget(
        [
            'options' => [
                'class' => 'alert-success',
            ],
            'body' => Yii::t('usuario', 'Assignments have been updated'),
        ]
    ) ?>

<?php endif ?>

<?php $form = ActiveForm::begin(
    [
        'enableClientValidation' => false,
        'enableAjaxValidation' => false,
    ]
) ?>

<?= Html::activeHiddenInput($model, 'user_id') ?>

<?= $form->field($model, 'items')->widget(Select2::class, [
    'data' => $availableItems,
    'options' => [
        'multiple' => true
    ],
    'pluginOptions' => [
        'allowClear' => true
    ],
]);
?>
<div class="d-grid gap-2">
    <?= Html::submitButton(Yii::t('usuario', 'Update assignments'), ['class' => 'btn btn-success  mt-3']) ?>
</div>
<?php ActiveForm::end() ?>
