How to Use ReCaptcha Widget
============================

We have included a [Google ReCAPTCHA](https://developers.google.com/recaptcha) widget if you wish to use it instead of 
Yii's captcha. The widget is based on reCaptcha v2.0.

To make use of the widget you need to: 

- [Signup for a reCaptcha API site key](https://www.google.com/recaptcha/admin#createsite)
- Configure the `ReCaptchaComponent` on the `components` section of your application configuration
- Override the Form class you wish to add the captcha rule to
- Override the view where the form is rendering 
- Configure Module and Application

Configuring the ReCaptchaComponent 
----------------------------------

Once you have the API site key you will also be displayed a secret key. You have to configure the component as follows: 

```php 
'components' => [
    'recaptcha' => [ // *important* this name must be like this
        'class' => 'Da\User\Component\ReCaptchaComponent',
        'key' => 'yourSiteKey',
        'secret' => 'secretKeyGivenByGoogle
    ]
]
```
  
Override the Form 
-----------------

For the sake of the example, we are going to override the `Da\User\Form\RecoveryForm` class. Create a new file `RecoveryForm`
add it to @app/models/Forms/ and put the following in it:

```
<?php 
namespace app\models\Forms;

use Da\User\Form\RecoveryForm as BaseForm;

class RecoveryForm extends BaseForm {

    public $captcha;

    public function rules() {

        $rules = parent::rules();

        $rules[] = [['captcha'], 'required'];
        $rules[] = [['captcha'], 'Da\User\Validator\ReCaptchaValidator'];

        return $rules;
    }
    
    public function scenarios()
    {
        return [
            self::SCENARIO_REQUEST => ['email', 'captcha'],
            self::SCENARIO_RESET => ['password'],
        ];
    }
}

```


Overriding the View
-------------------

Create a new file and name it `request.php` and add it in `@app/views/user/recovery`. Add the captcha widget to it: 

``` 
<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use Da\User\Widget\ReCaptchaWidget;

/**
 * @var yii\web\View $this
 * @var yii\widgets\ActiveForm $form
 * @var \Da\User\Form\RecoveryForm $model
 */

$this->title = Yii::t('usuario', 'Recover your password');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-md-4 col-md-offset-4 col-sm-6 col-sm-offset-3">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><?= Html::encode($this->title) ?></h3>
            </div>
            <div class="panel-body">
                <?php $form = ActiveForm::begin(
                    [
                        'id' => $model->formName(),
                        'enableAjaxValidation' => false,
                        'enableClientValidation' => false,
                    ]
                ); ?>

                <?= $form->field($model, 'email')->textInput(['autofocus' => true]) ?>
                
                <?= $form->field($model, 'captcha')->widget(ReCaptchaWidget::className(), ['theme' => 'light']) ?>

                <?= Html::submitButton(Yii::t('usuario', 'Continue'), ['class' => 'btn btn-primary btn-block']) ?><br>

                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>

```

Configure Module and Application
--------------------------------

Finally, we have to configure the module and the application to ensure is using our form and our view: 

```php

// ... 

'modules' => [
    'user' => [
        'class' => Da\User\Module::class,
        'classMap' => [
             'RecoveryForm' => 'app\models\Forms\RecoveryForm'
        ], 
    ]
], 

// ...

'components' => [
    'view' => [
        'theme' => [
            'pathMap' => [
                '@Da/User/resources/views' => '@app/views/user'
            ]
        ]
    ]
]
```

Notes For Other Forms
---------------------

The outward facing forms (i.e. forms that you don't need to login to use) also include `registrationForm`, `resendForm`. 

- All three forms need `'enableAjaxValidation' => false` in the view override.
- `registrationForm` & `resendForm` do not need `scenarios()` in the form override.
- `registrationForm` needs fix #347 to work.

© [2amigos](http://www.2amigos.us/) 2013-2019
