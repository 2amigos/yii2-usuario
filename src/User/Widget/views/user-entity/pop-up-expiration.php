<?php
use yii\helpers\Html;

if (empty($popupData)) {
    return;
}

$this->registerCss(<<<CSS
    .popup {
        position: fixed;
        right: 20px;
        background-color: #ffffff;
        border: 1px solid #000000;
        padding: 15px;
        margin-bottom: 10px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        z-index: 9999;
        border-radius: 6px;
        font-family: Arial, sans-serif;
        max-width: 300px;
        display: none;
    }

    .popup .close-btn {
        position: absolute;
        top: 5px;
        right: 10px;
        cursor: pointer;
        font-weight: bold;
        color: #000;
    }
CSS
);

$this->registerJs(<<<JS
    function getCookie(name) {
        const value = "; " + document.cookie;
        const parts = value.split("; " + name + "=");
        if (parts.length === 2) return parts.pop().split(";").shift();
    }

    function setCookie(name, value, days) {
        const date = new Date();
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        document.cookie = name + "=" + value + ";expires=" + date.toUTCString() + ";path=/";
    }

    document.querySelectorAll('.popup').forEach(function(popup) {
        const id = popup.dataset.popupId;
        const disabled = getCookie('popup_disabled_' + id);
        let count = parseInt(getCookie('popup_dismiss_count_' + id) || '0');

        if (disabled !== '1') {
            popup.style.display = 'block';

            popup.querySelector('.close-btn').addEventListener('click', function() {
                popup.style.display = 'none';
                count++;
                setCookie('popup_dismiss_count_' + id, count, 32);
                if (count >= 3) {
                    setCookie('popup_disabled_' + id, '1', 32);
                }
            });

            setTimeout(() => {
                popup.style.display = 'none';
            }, 6000);
        }
    });
JS
);
?>

<div class="pop-up-expiration">
    <?php foreach ($popupData as $i => $passkey): ?>
        <?php
        if (!isset($passkey['id'])) {
            continue;
        }
        $popupId = $passkey['id'];
        ?>
        <div class="popup" data-popup-id="<?= Html::encode($popupId) ?>" style="top: <?= 20 + $i * 140 ?>px;">
            <span class="close-btn">×</span>
            <strong>Passkey: <?= Html::encode($passkey['name']) ?></strong><br>
            <?= Yii::t('usuario','expires in')?>
            <strong><?= Yii::$app->formatter->asDuration($passkey['daysLeft'] * 86400) ?></strong>
            (<?= Yii::$app->formatter->asDate($passkey['expirationDate']) ?>)
            <?= Html::a('Manage your passkeys', \yii\helpers\Url::to(['/user/user-entity/index-passkey']))?>
        </div>
    <?php endforeach; ?>
</div>
