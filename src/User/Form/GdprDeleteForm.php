<?php


namespace Da\User\Form;


use yii\base\Model;
use Yii;
use Da\User\Model\User;
use Da\User\Helper\SecurityHelper;

class GdprDeleteForm extends Model
{
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
                    if ($this->user === null ||
                        !$this->securityHelper->validatePassword($this->password, $this->user->password_hash)
                    ) {
                        $this->addError($attribute, Yii::t('usuario', 'Invalid login or password'));
                    }
                },
            ]
        ];
    }

}