<?php

/*
 * This file is part of the 2amigos/yii2-usuario project.
 *
 * (c) 2amigOS! <http://2amigos.us/>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

use Da\User\Widget\ConnectWidget;
use yii\helpers\Html;

/**
 * @var yii\web\View           $this
 * @var yii\widgets\ActiveForm $form
 * @var \Da\User\Model\User    $user
 */

$this->title = Yii::t('usuario', 'Networks');
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="clearfix"></div>

<?= $this->render('/shared/_alert', ['module' => Yii::$app->getModule('user')]) ?>

<div class="row">
    <div class="col-md-3">
        <?= $this->render('/settings/_menu') ?>
    </div>
    <div class="col-md-9">
        <div class="panel panel-default">
            <div class="panel-heading">
                <?= Html::encode($this->title) ?>
            </div>
            <div class="panel-body">
                <div class="alert alert-info">
                    <p><?= Yii::t('usuario', 'You can connect multiple accounts to be able to log in using them') ?>
                        .</p>
                </div>
                <?php $auth = ConnectWidget::begin(
                    [
                        'baseAuthUrl' => ['/user/security/auth'],
                        'accounts' => $user->socialNetworkAccounts,
                        'autoRender' => false,
                        'popupMode' => false,
                    ]
                ) ?>
                <table class="table">
                    <?php foreach ($auth->getClients() as $client): ?>
                        <tr>
                            <td style="width: 32px; vertical-align: middle">
                                <?= Html::tag('span', '', ['class' => 'auth-icon ' . $client->getName()]) ?>
                            </td>
                            <td style="vertical-align: middle">
                                <strong><?= $client->getTitle() ?></strong>
                            </td>
                            <td style="width: 120px">
                                <?= $auth->isConnected($client) ?
                                    Html::a(
                                        Yii::t('usuario', 'Disconnect'),
                                        $auth->createClientUrl($client),
                                        [
                                            'class' => 'btn btn-danger btn-block',
                                            'data-method' => 'post',
                                        ]
                                    ) :
                                    Html::a(
                                        Yii::t('usuario', 'Connect'),
                                        $auth->createClientUrl($client),
                                        [
                                            'class' => 'btn btn-success btn-block',
                                        ]
                                    )
                                ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </table>
                <?php ConnectWidget::end() ?>
            </div>
        </div>
    </div>
</div>
