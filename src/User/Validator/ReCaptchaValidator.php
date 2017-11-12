<?php

/*
 * This file is part of the 2amigos/yii2-usuario project.
 *
 * (c) 2amigOS! <http://2amigos.us/>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Da\User\Validator;

use Da\User\Component\ReCaptchaComponent;
use Yii;
use yii\base\InvalidConfigException;
use yii\validators\Validator;

class ReCaptchaValidator extends Validator
{
    /**
     * @inheritdoc
     */
    public $skipOnEmpty = false;
    /**
     * @var string the message for client validation
     */
    public $notCheckedMessage;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        if (null === $this->message) {
            $this->message = Yii::t('usuario', 'The verification code is incorrect.');
        }
    }

    /**
     * @inheritdoc
     */
    public function clientValidateAttribute($model, $attribute, $view)
    {
        $message = addslashes(
            $this->notCheckedMessage ?: Yii::t('usuario', '{0} cannot be blank.', $model->getAttributeLabel($attribute))
        );

        return "(function(messages){if(!grecaptcha.getResponse()){messages.push('{$message}');}})(messages);";
    }

    /**
     * @inheritdoc
     *
     * @throws InvalidConfigException
     */
    protected function validateValue($value)
    {
        if (empty($value)) {
            if (!($value = Yii::$app->request->post('g-recaptcha-response'))) {
                return [$this->message, []];
            }
        }
        /** @var ReCaptchaComponent $recaptcha */
        $recaptcha = Yii::$app->get('recaptcha');

        return $recaptcha->verify($value) ? null : [$this->message, []];
    }
}
