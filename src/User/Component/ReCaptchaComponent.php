<?php

/*
 * This file is part of the 2amigos/yii2-usuario project.
 *
 * (c) 2amigOS! <http://2amigos.us/>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Da\User\Component;

use Yii;
use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\httpclient\Client;

class ReCaptchaComponent extends Component
{
    /**
     * @var string the ReCAPTCHA sitekey
     */
    public $key;
    /**
     * @var string the shared key between the site and ReCAPTCHA
     */
    public $secret;

    /**
     * @inheritdoc
     *
     * @throws InvalidConfigException
     */
    public function init()
    {
        if (empty($this->key)) {
            throw new InvalidConfigException(Yii::t('usuario', 'Required "key" cannot be empty.'));
        }

        if (empty($this->secret)) {
            throw new InvalidConfigException(Yii::t('usuario', 'Required "secret" cannot be empty.'));
        }

        parent::init();
    }

    /**
     * Verifies whether a user response is valid or not.
     *
     * @param $value
     *
     * @return bool
     */
    public function verify($value)
    {
        $response = (new Client(
            [
                'baseUrl' => 'https://www.google.com/recaptcha/api',
                'responseConfig' => [
                    'format' => Client::FORMAT_JSON
                ]
            ]
        ))
            ->get(
                'siteverify',
                [
                    'secret' => $this->secret,
                    'response' => $value,
                    'remoteip' => Yii::$app->request->getUserIP()
                ]
            )
            ->send();

        return $response->getData()['success'] ? : false;
    }
}
