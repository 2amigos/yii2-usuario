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

use Da\TwoFA\Exception\InvalidSecretKeyException;
use Da\User\Model\User;
use Da\User\Service\TwoFactorSmsCodeGeneratorService;
use Da\User\Traits\ContainerAwareTrait;
use Da\User\Traits\ModuleAwareTrait;
use Yii;
use yii\helpers\ArrayHelper;

class TwoFactorTextMessageValidator extends TwoFactorCodeValidator
{
    use ContainerAwareTrait;
    use ModuleAwareTrait;

    protected $user;
    protected $code;
    protected $cycles;
    protected $type;

    /**
     * TwoFactorCodeValidator constructor.
     *
     * @param User $user
     * @param $code
     * @param int $cycles
     */
    public function __construct(User $user, $code, $cycles = 0)
    {
        parent::__construct($user, $code, $cycles);
        $this->type = 'sms';
    }

    /**
     * @throws InvalidSecretKeyException
     * @return bool|int
     *
     */
    public function validate()
    {
        if (is_null($this->code) || $this->code == '') {
            return false;
        }
        $smsCodeTime = new \DateTime(Yii::$app->session->get("sms_code_time"));
        $currentTime = new \DateTime('now');
        $interval = $currentTime->getTimestamp() - $smsCodeTime->getTimestamp();
        $module = $this->getModule();
        $validators = $module->twoFactorAuthenticationValidators;
        $codeDurationTime = ArrayHelper::getValue($validators, $this->type.'.codeDurationTime', 300);

        if ($interval > $codeDurationTime) {
            return false;
        }
        $smsCode = Yii::$app->session->get("sms_code");
        return $this->code == $smsCode;
    }

    /**
     * @return string
     *
     */
    public function getSuccessMessage()
    {
        return Yii::t('usuario', 'Two factor authentication successfully enabled.');
    }

    /**
     *
     * @param  mixed  $codeDurationTime
     * @return string
     */
    public function getUnsuccessMessage($codeDurationTime)
    {
        return Yii::t('usuario', 'Please, enter the right code. The code is valid for {0} seconds. If you want to get a new code, please close this window and repeat the enabling request.', [$codeDurationTime]);
    }

    /**
     *
     * @param  mixed  $codeDurationTime
     * @return string
     */
    public function getUnsuccessLoginMessage($codeDurationTime)
    {
        return Yii::t('usuario', 'Please, enter the right code. The code is valid for {0} seconds. If you want to get a new code, please click on \'Cancel\' and repeat the login request.', [$codeDurationTime]);
    }

    /**
    * @return string
    *
    */
    public function generateCode()
    {
        $object = $this->make(TwoFactorSmsCodeGeneratorService::class, [$this->user]);
        return $object->run();
    }
}
