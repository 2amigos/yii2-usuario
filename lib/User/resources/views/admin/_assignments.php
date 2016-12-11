<?php

use Da\User\Widget\AssignmentsWidget;

/**
 * @var yii\web\View $this
 * @var \Da\User\Model\User $user
 */

?>

<?php $this->beginContent('@Da/User/resources/views/admin/update.php', ['user' => $user]) ?>

<?= yii\bootstrap\Alert::widget(
    [
        'options' => [
            'class' => 'alert-info alert-dismissible',
        ],
        'body' => Yii::t('user', 'You can assign multiple roles or permissions to user by using the form below'),
    ]
) ?>

<?= AssignmentsWidget::widget(['userId' => $user->id]) ?>

<?php $this->endContent() ?>
