
<?php
use yii\helpers\Html;
use yii\helpers\Url;
?>

<!-- Modal -->
<div id="passkeyModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="passkeyModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="passkeyModalLabel">Faster Login with Passkey</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body text-center">
        <p>Would you like to create a Passkey for faster and safer access?</p>
        <div class="form-check mt-3 mb-2">
            <input type="checkbox" class="form-check-input" id="dontShowAgain">
            <label class="form-check-label" for="dontShowAgain">Don't show this again</label>
        </div>
      </div>
      <div class="modal-footer justify-content-center">
        <?= Html::a('Yes, create one', ['/user/user-entity/create-passkey'], ['class' => 'btn btn-success']) ?>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">No, thanks</button>
      </div>
    </div>
  </div>
</div>

<?php
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
    if (!getCookie("hidePasskeyModal")) {
        $('#passkeyModal').modal('show');
    }

    $('#dontShowAgain').on('change', function() {
        if ($(this).is(':checked')) {
            setCookie("hidePasskeyModal", "1", 365);
        }
    });
});
JS;

$this->registerJs($js);
?>
