<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Manage Your Passkeys';
?>

<div class="passkey-index">
    <h1><?= Html::encode($this->title) ?></h1>

    <div class="mb-4">
        <?= Html::a('+ Add New Passkey', ['create-passkey'], ['class' => 'btn btn-success']) ?>
    </div>

    <?php if ($dataProvider->getCount() > 0): ?>
        <?= $this->render('view', ['dataProvider' => $dataProvider]) ?>
    <?php else: ?>
        <p>No passkeys found.</p>
    <?php endif; ?>
</div>
