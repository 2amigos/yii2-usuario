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
use Yii;
use yii\helpers\ArrayHelper;
use Da\User\Traits\ContainerAwareTrait;
use Da\User\Service\TwoFactorEmailCodeGeneratorService;


class TwoFactorEmailValidator extends TwoFactorCodeValidator
{
    use ContainerAwareTrait;
    
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
        $this->user = $user;
        $this->code = $code;
        $this->cycles = $cycles;
        $this->type = 'email';
    }

    /**
     * @throws InvalidSecretKeyException
     * @return bool|int
     *
     */
    public function validate()
    {
        if(is_null($this->code) ||  $this->code == '' )
            return false;
        $emailCodeTime = new \DateTime(Yii::$app->session->get("email_code_time"));
        $currentTime = new \DateTime('now');
        $interval = $currentTime->getTimestamp()-$emailCodeTime->getTimestamp();
       
        $module = Yii::$app->getModule('user');
        $validators = $module->twoFactorAuthenticationValidators;
        $codeDurationTime = ArrayHelper::getValue($validators,$this->type.'.codeDurationTime', 300);
        
        if($interval > $codeDurationTime ){
            return false;
        }
        $emailCode = Yii::$app->session->get("email_code");
        return $this->code==$emailCode;
    }

    /**
     * @return bool
     *
     */
    public function isValidationCodeToBeSent()
    {
        return true;
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
     * @return string
     *
     */
    public function getUnsuccessMessage($codeDurationTime)
    {
        return Yii::t('usuario', 'Please, enter the right code. The code is valid for {0} seconds. If you want to get a new code, please close this window and repeat the enabling request.', [$codeDurationTime]);
    }

     /**
     * @return string
     *
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
        return $this->make(TwoFactorEmailCodeGeneratorService::class,$this->user)->run();
    }
}
