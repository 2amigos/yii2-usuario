<?php
use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;
/** @var \yii\base\DynamicModel $model */
/** @var string $gdpr_consent_hint */
?>

<div class="row">
    <div class="col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2">
        <?php $form = ActiveForm::begin(
            [
                'id' => $model->formName(),
            ]
        ); ?>
            <div class="card bg-info give-consent-panel">
                <div class="card-header">
                <h1 class="m-0"><?= Yii::t('usuario', 'Data privacy') ?></h1>
            </div>
            <div class="card-body">

                <p><?= Yii::t('usuario', 'According to the European General Data Protection Regulation (GDPR) we need your consent to work with your personal data.') ?></p>
                <p><?php Yii::t('usuario', 'Unfortunately, you can not work with this site without giving us consent to process your data.') ?></p>

                <?= $form->field($model, 'gdpr_consent')->checkbox(['value' => 1, 'label' => $gdpr_consent_hint])?>

            </div>
            <div class="card-footer">
                <?= Html::submitButton(Yii::t('usuario', 'Submit'), ['class' => 'btn btn-success']) ?>
                <p class="pull-right small"><?= Html::a(Yii::t('usuario', 'Account details'), ['/user/settings']) ?></p>
            </div>
        </div>
        <?php ActiveForm::end(); ?>

    </div>
</div>
