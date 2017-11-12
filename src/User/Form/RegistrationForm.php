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

use Da\User\Model\User;
use Da\User\Traits\ContainerAwareTrait;
use Da\User\Traits\ModuleAwareTrait;
use Yii;
use yii\base\Model;

class RegistrationForm extends Model
{
    use ModuleAwareTrait;
    use ContainerAwareTrait;

    /**
     * @var string User email address
     */
    public $email;
    /**
     * @var string Username
     */
    public $username;
    /**
     * @var string Password
     */
    public $password;

    /**
     * {@inheritdoc}
     *
     * @throws \Exception
     */
    public function rules()
    {
        /** @var User $user */
        $user = $this->getClassMap()->get(User::class);

        return [
            // username rules
            'usernameLength' => ['username', 'string', 'min' => 3, 'max' => 255],
            'usernameTrim' => ['username', 'filter', 'filter' => 'trim'],
            'usernamePattern' => ['username', 'match', 'pattern' => '/^[-a-zA-Z0-9_\.@]+$/'],
            'usernameRequired' => ['username', 'required'],
            'usernameUnique' => [
                'username',
                'unique',
                'targetClass' => $user,
                'message' => Yii::t('usuario', 'This username has already been taken'),
            ],
            // email rules
            'emailTrim' => ['email', 'filter', 'filter' => 'trim'],
            'emailRequired' => ['email', 'required'],
            'emailPattern' => ['email', 'email'],
            'emailUnique' => [
                'email',
                'unique',
                'targetClass' => $user,
                'message' => Yii::t('usuario', 'This email address has already been taken'),
            ],
            // password rules
            'passwordRequired' => ['password', 'required', 'skipOnEmpty' => $this->module->generatePasswords],
            'passwordLength' => ['password', 'string', 'min' => 6, 'max' => 72],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'email' => Yii::t('usuario', 'Email'),
            'username' => Yii::t('usuario', 'Username'),
            'password' => Yii::t('usuario', 'Password'),
        ];
    }
}
