<?php

use yii\helpers\Html;

$this->title = Yii::t('usuario', 'Privacy settings');

?>

<div class="row">
    <div class="col-md-3">
        <?= $this->render('_menu') ?>
    </div>
    <div class="col-md-9">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><?= Html::encode($this->title) ?></h3>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-6">
                        <h3><?= Yii::t('usuario', 'Export my data') ?></h3>
                        <p><?= Yii::t(
                                'usuario',
                                'Here you can download your personal data in a comma separated values format.'
                            ) ?>
                        </p>
                        <?= Html::a(Yii::t('usuario', 'Download my data'),
                            ['/user/settings/export'],
                            [
                                'class' => 'btn btn-info',
                                'target' => '_blank'
                            ])
                        ?>
                    </div>
                    <div class="col-md-6">
                        <h3><?= Yii::t('usuario', 'Delete my account') ?></h3>
                        <p><?= Yii::t(
                                'usuario',
                                'This will remove your personal data from this site. You will no longer be able to sign in.'
                            ) ?>
                        </p>
                        <?= Html::a(Yii::t('usuario', 'Delete'),
                            ['/user/settings/delete'],
                            ['class' => 'btn btn-danger'])
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
