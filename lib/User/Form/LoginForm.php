<?php

namespace Da\User\Form;

use Da\User\Helper\SecurityHelper;
use Da\User\Model\User;
use Da\User\Query\UserQuery;
use Da\User\Traits\ModuleTrait;
use Yii;
use yii\base\Model;

class LoginForm extends Model
{
    use ModuleTrait;

    /**
     * @var string login User's email or username
     */
    public $login;
    /**
     * @var string User's password
     */
    public $password;
    /**
     * @var bool whether to remember User's login
     */
    public $rememberMe = false;
    /**
     * @var User
     */
    protected $user;
    /**
     * @var UserQuery
     */
    protected $query;
    /**
     * @var SecurityHelper
     */
    protected $securityHelper;

    /**
     * @param UserQuery      $query
     * @param SecurityHelper $securityHelper
     * @param array          $config
     */
    public function __construct(UserQuery $query, SecurityHelper $securityHelper, $config = [])
    {
        $this->query = $query;
        $this->securityHelper = $securityHelper;
        parent::__construct($config);
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'login' => Yii::t('user', 'Login'),
            'password' => Yii::t('user', 'Password'),
            'rememberMe' => Yii::t('user', 'Remember me next time'),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            'requiredFields' => [['login', 'password'], 'required'],
            'loginTrim' => ['login', 'trim'],
            'passwordValidate' => [
                'password',
                function ($attribute) {
                    if ($this->user === null ||
                        !$this->securityHelper->validatePassword($this->password, $this->user->password_hash)
                    ) {
                        $this->addError($attribute, Yii::t('user', 'Invalid login or password'));
                    }
                },
            ],
            'confirmationValidate' => [
                'login',
                function ($attribute) {
                    if ($this->user !== null) {
                        $module = $this->getModule();
                        $confirmationRequired = $module->enableEmailConfirmation && !$module->allowUnconfirmedEmailLogin;
                        if ($confirmationRequired && !$this->user->getIsConfirmed()) {
                            $this->addError($attribute, Yii::t('user', 'You need to confirm your email address'));
                        }
                        if ($this->user->getIsBlocked()) {
                            $this->addError($attribute, Yii::t('user', 'Your account has been blocked'));
                        }
                    }
                },
            ],
            'rememberMe' => ['rememberMe', 'boolean'],
        ];
    }

    /**
     * Validates form and logs the user in.
     *
     * @return bool whether the user is logged in successfully
     */
    public function login()
    {
        if ($this->validate()) {
            $duration = $this->rememberMe ? $this->module->rememberLoginLifespan : 0;

            return Yii::$app->getUser()->login($this->user, $duration);
        } else {
            return false;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function beforeValidate()
    {
        if (parent::beforeValidate()) {
            $this->user = $this->query->whereUsernameOrEmail(trim($this->login))->one();

            return true;
        }

        return false;
    }
}
