<?php
use yii\helpers\Html;
use yii\helpers\Url;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

?>
<div id="tflink" style="display: none;" class="alert alert-success"> 
    <?php echo Yii::t('usuario', 'Now you can resume the login process') ?> <?php echo Html::a(Yii::t('usuario', 'Login'), ['/user/security/login'])?>
</div>
<?php echo $this->render('@Da/User/resources/views/shared/_alert', ['module' => Yii::$app->getModule('user')]) ?>
<div class="modal fade" id="tfmodal" tabindex="-1" role="dialog" aria-labelledby="tfamodalLabel"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">
                    <?php echo Yii::t('usuario', 'Two Factor Authentication (2FA)') ?></h4>
            </div>
            <div class="modal-body">
                ...
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">
                    <?php echo Yii::t('usuario', 'Close') ?>
                </button>
            </div>
        </div>
    </div>
</div>
<div class="panel panel-info">
    <div class="panel-heading">
        <h3 class="panel-title"><?php echo Yii::t('usuario', 'Two Factor Authentication (2FA)') ?></h3>
    </div>
    <div class="panel-body">
        <p>
            <?php echo Yii::t('usuario', 'Two factor authentication protects you in case of stolen credentials') ?>.
        </p>

            <?php echo Html::a(
                Yii::t('usuario', 'Enable two factor authentication'),
                '#tfmodal',
                [
                    'id' => 'enable_tf_btn',
                    'class' => 'btn btn-info ',
                    'data-toggle' => 'modal',
                    'data-target' => '#tfmodal',
                ]
);
            ?>

    </div>
</div>

<?php
    // This script should be in fact in a module as an external file
    // consider overriding this view and include your very own approach
    $uri = Url::to(['/user/settings/two-factor-forced', 'id' => $user_id]);
    $verify = Url::to(['/user/settings/two-factor-enable-forced', 'id' => $user_id]);

    $js = <<<JS
$('#tfmodal')
    .on('show.bs.modal', function(){
        if(!$('img#qrCode').length) {
            $(this).find('.modal-body').load('{$uri}');
        } else {
            $('input#tfcode').val('');
        }
    });

$(document)
    .on('click', '.btn-submit-code', function(e) {
        e.preventDefault();
        var btn = $(this);
        btn.prop('disabled', true);
        $.getJSON('{$verify}', {code: $('#tfcode').val()}, function(data){
            btn.prop('disabled', false);
            if(data.success) {
                $('#enable_tf_btn, #disable_tf_btn').toggleClass('hide');
                $('#tfmessage').removeClass('alert-danger').addClass('alert-success').find('p').text(data.message);
                $('#tflink').toggleClass('show');
                setTimeout(function() { $('#tfmodal').modal('hide'); }, 2000);
            } else {
                $('input#tfcode').val('');
                $('#tfmessage').removeClass('alert-info').addClass('alert-danger').find('p').text(data.message);
            }
        }).fail(function(){ btn.prop('disabled', false); });
    });
JS;

    $this->registerJs($js);
?>
