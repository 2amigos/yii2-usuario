<?php

/**
 * @var \Da\User\Model\User $user
 * @var \Da\User\Model\Token $token
 * @var \Da\User\Module $module
 */
?>
<?= Yii::t('user', 'Hello') ?>,

<?= Yii::t('user', 'Your account on {0} has been created', Yii::$app->name) ?>.
<?php if ($module->generatePasswords): ?>
    <?= Yii::t('user', 'We have generated a password for you') ?>:
    <?= $user->password ?>
<?php endif ?>

<?php if ($token !== null): ?>
    <?= Yii::t('user', 'In order to complete your registration, please click the link below') ?>.
    <?= $token->url ?>
    <?= Yii::t('user', 'If you cannot click the link, please try pasting the text into your browser') ?>.
<?php endif ?>

<?= Yii::t('user', 'If you did not make this request you can ignore this email') ?>.
