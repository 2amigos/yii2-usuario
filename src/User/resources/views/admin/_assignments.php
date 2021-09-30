<?php

/*
 * This file is part of the 2amigos/yii2-usuario project.
 *
 * (c) 2amigOS! <http://2amigos.us/>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

use Da\User\Widget\AssignmentsWidget;

/* @var yii\web\View $this */
/* @var Da\User\Model\User $user */
/* @var string[] $params */

?>

<?php $this->beginContent('@Da/User/resources/views/admin/update.php', ['user' => $user]) ?>

<?= yii\bootstrap4\Alert::widget(
    [
        'options' => [
            'class' => 'alert-info alert-dismissible',
        ],
        'body' => Yii::t('usuario', 'You can assign multiple roles or permissions to user by using the form below'),
    ]
) ?>

<?= AssignmentsWidget::widget(['userId' => $user->id, 'params' => $params]) ?>

<?php $this->endContent() ?>
