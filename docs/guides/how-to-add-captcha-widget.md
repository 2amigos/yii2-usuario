How to Add Captcha Widget
=========================

In order to add the Yii 2 captcha widget you need to: 

- Override the Form class you wish to add the captcha rule to
- Override the view where the form is rendering 
- Add captcha action to a controller
- Configure Module and Application


Override the Form 
-----------------

For the sake of the example, we are going to override the `Da\User\Form\RecoveryForm` class: 

```php 
namespace app\forms;

class RecoveryForm extends Da\User\Form\RecoveryForm {
    
    public $captcha;
    
    public function rules() {
    
        $rules = parent::rules();
        
        $rules[] = ['captcha', 'required'];
        $rules[] = ['captcha', 'captcha'];
        
        return $rules;
    }
}

```

Overriding the View
-------------------

Create a new file and name it `request.php` and add it in `@app/views/user/recovery`. Add the captcha widget to it: 

```php 
<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\captcha\Captcha;

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
                        'enableAjaxValidation' => true,
                        'enableClientValidation' => false,
                    ]
                ); ?>

                <?= $form->field($model, 'email')->textInput(['autofocus' => true]) ?>
                
                <?= $form->field($model, 'captcha')
                    ->widget(Captcha::className(), ['captchaAction' => ['/site/captcha']]) ?>

                <?= Html::submitButton(Yii::t('usuario', 'Continue'), ['class' => 'btn btn-primary btn-block']) ?><br>

                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>

```

Add Captcha Action to a Controller
----------------------------------

```php 

namespace app\controllers;

class RecoveryController extends \yii\web\Controller
{
    // ...
    
    public function actions()
    {
        return [
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
            ],
        ];
    }
    
    // ...
}

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
            'RecoveryForm' => 'app\forms\RecoveryForm'
        ], 
        'controllerMap' => [
            'recovery' => [
                 'class' => '\app\controllers\RecoveryController' 
             ]
        ]
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

© [2amigos](http://www.2amigos.us/) 2013-2019
