<?php
use yii\helpers\Html;
use yii\helpers\Url;

$this->registerCss(<<<CSS
#passkeyToast {
    position: fixed;
    top: 20px;
    right: 20px;
    background-color: #f8f9fa;
    color: #212529;
    border: 1px solid #dee2e6;
    padding: 15px 20px 10px 20px;
    margin-bottom: 10px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    z-index: 9999;
    border-radius: 8px;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
    max-width: 300px;
    font-size: 14px;
    display: none;
}

#passkeyToast .close-btn, #closePasskeyToast {
    background: transparent;
    border: none;
    font-size: 16px;
    line-height: 1;
    color: #000;
    cursor: pointer;
    margin-left: 10px;
}
CSS);

$userId = Yii::$app->user->id ?? 'guest';

$js = <<<JS
function getCookie(name) {
    const value = "; " + document.cookie;
    const parts = value.split("; " + name + "=");
    if (parts.length === 2) return parts.pop().split(";").shift();
}

function setCookie(name, value, days) {
    let expires = "";
    if (days) {
        const date = new Date();
        date.setTime(date.getTime() + (days*24*60*60*1000));
        expires = "; expires=" + date.toUTCString();
    }
    document.cookie = name + "=" + (value || "") + expires + "; path=/";
}

$(document).ready(function() {
    const cookieName = "hidePasskeyToast_{$userId}";

    if (!getCookie(cookieName)) {
        $('#passkeyToast').fadeIn();

        setTimeout(function() {
            $('#passkeyToast').fadeOut();
        }, 6000);
    }

    $('#dontShowAgain').on('change', function() {
        if ($(this).is(':checked')) {
            setCookie(cookieName, "1", 365);
        }
    });

    $('#closePasskeyToast').on('click', function() {
        $('#passkeyToast').fadeOut();
    });
});
JS;

$this->registerJs($js);
?>

<div id="passkeyToast">
    <div style="display: flex; justify-content: space-between; align-items: start;">
        <div style="flex-grow: 1;">
            <strong><?=Yii::t('usuario','Use a Passkey')?></strong>
            <p style="margin: 5px 0 10px;"><?=Yii::t('usuario','Create a passkey for a faster and safer login.')?></p>
            <?= Html::a(Yii::t('usuario','Create now'), ['/user/user-entity/create-passkey'], [
                'class' => 'btn btn-success btn-sm mb-2',
                'style' => 'font-size: 13px;',
            ]) ?>
            <div class="form-check" style="font-size: 12px;">
                <input type="checkbox" class="form-check-input" id="dontShowAgain">
                <label class="form-check-label" for="dontShowAgain"><?=Yii::t('usuario','Don\'t show this again')?></label>
            </div>
        </div>
        <button type="button" id="closePasskeyToast" aria-label=<?=Yii::t('usuario','Close')?>>&times;</button>
    </div>
</div>
