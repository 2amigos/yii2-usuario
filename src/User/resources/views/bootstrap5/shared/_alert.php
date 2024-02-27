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

/** @var \Da\User\Module $module */

?>

<?php if ($module->enableFlashMessages): ?>
    <div class="row">
        <div class="col-xs-12">
            <?php foreach (Yii::$app->session->getAllFlashes(true) as $type => $message): ?>
                <?php if (in_array($type, ['success', 'danger', 'warning', 'info'], true)): ?>
                    <?= Alert::widget(
                        [
                            'options' => ['class' => 'alert-dismissible alert-' . $type],
                            'body' => $message,
                        ]
                    ) ?>
                <?php endif ?>
            <?php endforeach ?>
        </div>
    </div>
<?php endif ?>
