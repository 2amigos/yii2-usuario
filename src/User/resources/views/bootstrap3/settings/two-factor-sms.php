<?php
/*
* This file is part of the 2amigos/yii2-usuario-app project.
*
* (c) 2amigOS! <http://2amigos.us/>
*
* For the full copyright and license information, please view
* the LICENSE file that was distributed with this source code.
*/

/** @var string $id */
/** @var string $uri */
/** @var string $mobilePhone */
?>

<div id="phonenumbersection">
    <div class="alert alert-info" id="tfmessagephone">
        <p>
            <?= Yii::t(
                'usuario',
                'Insert the mobile phone number where you want to receive text message in international format'
            ) ?>
        </p>
    </div>

    <div class="row">
        <div class="col-md-offset-3 col-md-6 text-center">
            <div class="input-group">
                <input type="text" class="form-control" id="mobilephone" value="<?= $mobilePhone ?>" placeholder="<?= Yii::t('usuario', 'Mobile phone number') ?>"/>
                <span class="input-group-btn">
                    <button type="button" class="btn btn-primary btn-submit-mobile-phone">
                        <?= Yii::t('usuario', 'Insert') ?>
                    </button>
                </span>
            </div>
        </div>
    </div>
</div>

<div id="smssection" class="hide">
    <hr>
    <div class="alert alert-info" id="tfmessage">
        <p>
            <?= Yii::t(
                'usuario',
                'Insert the code you received by SMS.'
            ) ?>
        </p>
    </div>

    <div class="row">
        <div class="col-md-offset-3 col-md-6 text-center">
            <div></div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-offset-3 col-md-6 text-center">
            <div class="input-group">
                <input type="text" class="form-control" id="tfcode" placeholder="<?= Yii::t('usuario', 'Two factor authentication code by SMS') ?>"/>
                <span class="input-group-btn">
                    <button type="button" class="btn btn-primary btn-submit-code">
                        <?= Yii::t('usuario', 'Enable') ?>
                    </button>
                </span>
            </div>
        </div>
    </div>
</div>