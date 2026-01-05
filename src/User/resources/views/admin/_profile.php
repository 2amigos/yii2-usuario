<?php

/*
 * This file is part of the 2amigos/yii2-usuario project.
 *
 * (c) 2amigOS! <http://2amigos.us/>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

/**
 * @var yii\web\View           $this
 * @var \Da\User\Model\User    $user
 * @var \Da\User\Model\Profile $profile
 * @var \Da\User\Module $module
 */

$this->registerCss(<<<CSS
.profile-image-upload {
    display: flex;
    align-items: center;
    gap: 20px;
    margin-left: 222px;
}

.image-preview {
    flex-shrink: 0;
}

.profile-avatar {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid #e0e0e0;
}

.profile-avatar-placeholder {
    background-color: #f0f0f0;
    color: #999;
    display: flex;
    align-items: center;
    justify-content: center;
}

.profile-avatar-placeholder svg {
    width: 50%;
    height: 50%;
}
CSS);

?>

<?php $this->beginContent($module->viewPath. '/admin/update.php', ['user' => $user]) ?>

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

<div class="profile-image-upload">
    <div class="image-preview">
        <img src="<?= $profile->getImageUrl() ?>" alt="Profile Picture" class="profile-avatar">
    </div>
    <?= $form->field($profile, 'imageUpload')->fileInput(['accept' => 'image/*']) ?>
</div>
<?= $form->field($profile, 'name') ?>
<?= $form->field($profile, 'public_email') ?>
<?= $form->field($profile, 'website') ?>
<?= $form->field($profile, 'location') ?>
<?= $form->field($profile, 'gravatar_email') ?>
<?= $form->field($profile, 'bio')->textarea() ?>


<div class="form-group">
    <div class="col-lg-offset-3 col-lg-9">
        <?= Html::submitButton(Yii::t('usuario', 'Update'), ['class' => 'btn btn-block btn-success']) ?>
    </div>
</div>

<?php ActiveForm::end(); ?>

<?php $this->endContent() ?>
