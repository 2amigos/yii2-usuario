<?php

/*
 * This file is part of the 2amigos/yii2-usuario project.
 *
 * (c) 2amigOS! <http://2amigos.us/>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Da\User\Form;

use Da\User\Helper\SecurityHelper;
use Da\User\Model\User;
use Da\User\Traits\ContainerAwareTrait;
use Yii;
use yii\base\Model;

/**
 * Class GdprDeleteForm
 * @package Da\User\Form
 */
class GdprDeleteForm extends Model
{
    use ContainerAwareTrait;

    /**
     * @var string User's password
     */
    public $password;
    /**
     * @var SecurityHelper
     */
    protected $securityHelper;
    /**
     * @var User
     */
    protected $user;

    /**
     * @param SecurityHelper $securityHelper
     * @param array          $config
     */
    public function __construct(SecurityHelper $securityHelper, $config = [])
    {
        $this->securityHelper = $securityHelper;
        parent::__construct($config);
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            'requiredFields' => [['password'], 'required'],
            'passwordValidate' => [
                'password',
                function ($attribute) {
                    if (!$this->securityHelper
                        ->validatePassword($this->password, $this->getUser()->password_hash)
                    ) {
                        $this->addError($attribute, Yii::t('usuario', 'Invalid password'));
                    }
                },
            ]
        ];
    }

    /**
     * @return User|null|\yii\web\IdentityInterface
     */
    public function getUser()
    {
        if ($this->user == null) {
            $this->user = Yii::$app->user->identity;
        }

        return $this->user;
    }

    public function attributeLabels()
    {
        return [
            'password' => Yii::t('usuario', 'Password'),
        ];
    }
}
