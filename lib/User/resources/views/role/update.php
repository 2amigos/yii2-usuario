<?php

/**
 * @var \Da\User\Model\Role
 * @var $this               yii\web\View
 * @var $unassignedItems    string[]
 */
$this->title = Yii::t('user', 'Update role');
$this->params['breadcrumbs'][] = $this->title;

?>

<?php $this->beginContent('@Da/User/resources/views/shared/admin_layout.php') ?>

<?= $this->render(
    '_form',
    [
        'model' => $model,
        'unassignedItems' => $unassignedItems,
    ]
) ?>

<?php $this->endContent() ?>
