<?php

/*
 * This file is part of the 2amigos/yii2-usuario project.
 *
 * (c) 2amigOS! <http://2amigos.us/>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Da\User\Service;

use Da\TwoFA\Manager;
use Da\User\Contracts\ServiceInterface;
use Da\User\Factory\MailFactory;
use Da\User\Model\User;
use yetopen\smssender\SmsSenderInterface;
use Yii;
use yii\di\Instance;
use yii\helpers\ArrayHelper;

class TwoFactorSmsCodeGeneratorService implements ServiceInterface
{
    /**
     * @var User
     */
    protected $user;

    /**
     * @var Type
     */
    protected $type;

    /**
     * @var SmsSenderInterface
     */
    protected $smsSender;

    /**
     * TwoFactorSmsCodeGeneratorService constructor.
     *
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
        $this->type = 'sms';
        $module = Yii::$app->getModule('user');
        $validators = $module->twoFactorAuthenticationValidators;
        $smsSender = ArrayHelper::getValue($validators, 'sms'.'.smsSender');
        $this->smsSender = Instance::ensure($smsSender, SmsSenderInterface::class);
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        // generate key
        $code = random_int(0, 999999);
        $code = str_pad($code, 6, 0, STR_PAD_LEFT);
        // get the mobile phone of the user
        $user = $this->user;
        $mobilePhone = $user->getAuthTfMobilePhone();

        if (null === $mobilePhone || $mobilePhone == '') {
            return false;
        }
        // send sms
        $success = $this->smsSender->send($mobilePhone, $code);
        if ($success) {
            // put key in session
            Yii::$app->session->set("sms_code_time", date('Y-m-d H:i:s'));
            Yii::$app->session->set("sms_code", $code);
        } else {
            Yii::$app->session->addFlash('error', Yii::t('usuario', 'The sms sending failed, please check your configuration.'));
            return false;
        }
        return true;
    }
}
