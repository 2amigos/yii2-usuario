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
 * @var String $code
 */
?>
<?= Yii::t('usuario', 'Hello') ?>,

<?= Yii::t('usuario', 'This is the code to insert to enable two factor authentication') ?>:

<?= $code ?>

<?= Yii::t('usuario', 'If you did not make this request you can ignore this email') ?>.
