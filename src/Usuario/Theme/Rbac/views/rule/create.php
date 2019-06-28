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
 * @var \Da\User\Model\Rule $model
 * @var $this               yii\web\View
 * @var $unassignedItems    string[]
 */
$this->title = Yii::t('usuario', 'Create new rule');
$this->params['breadcrumbs'][] = $this->title;

?>

<?php $this->beginContent('@Da/User/resources/views/shared/admin_layout.php') ?>

<?= $this->render(
    '/rule/_form',
    [
        'model' => $model,
    ]
) ?>

<?php $this->endContent() ?>
