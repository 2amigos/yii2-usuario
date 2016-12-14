<?php

/**
 * @var \Da\User\Model\User
 * @var \Da\User\Model\Token $token
 */
?>
<?= Yii::t('user', 'Hello') ?>,

<?= Yii::t('user', 'Thank you for signing up on {0}', Yii::$app->name) ?>.
<?= Yii::t('user', 'In order to complete your registration, please click the link below') ?>.

<?= $token->url ?>

<?= Yii::t('user', 'If you cannot click the link, please try pasting the text into your browser') ?>.

<?= Yii::t('user', 'If you did not make this request you can ignore this email') ?>.
