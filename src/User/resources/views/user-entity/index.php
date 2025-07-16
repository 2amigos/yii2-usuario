<?php

use yii\helpers\Html;
use Da\User\Helper\TimezoneHelper;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = Yii::t('usuario','Manage Your Passkeys');
?>

<div class="passkey-index">
    <h1><?= Html::encode($this->title) ?></h1>

    <div class="mb-4">
        <?= Html::a(Yii::t('usuario','+ Add New Passkey'), ['create-passkey'], ['class' => 'btn btn-success']) ?>
    </div>

    <?= $this->render('view', ['dataProvider' => $dataProvider]) ?>
</div>
