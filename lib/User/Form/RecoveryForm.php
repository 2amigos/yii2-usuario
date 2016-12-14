<?php

namespace Da\User\Form;

use Da\User\Query\UserQuery;
use Da\User\Traits\ContainerTrait;
use Yii;
use yii\base\Model;

class RecoveryForm extends Model
{
    use ContainerTrait;

    const SCENARIO_REQUEST = 'request';
    const SCENARIO_RESET = 'reset';

    /**
     * @var string User's email
     */
    public $email;
    /**
     * @var string User's password
     */
    public $password;
    /**
     * @var UserQuery
     */
    protected $query;

    /**
     * @param UserQuery $query
     * @param array     $config
     */
    public function __construct(UserQuery $query, array $config = [])
    {
        $this->query = $query;
        parent::__construct($config);
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'email' => Yii::t('user', 'Email'),
            'password' => Yii::t('user', 'Password'),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        return [
            self::SCENARIO_REQUEST => ['email'],
            self::SCENARIO_RESET => ['password'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            'emailTrim' => ['email', 'filter', 'filter' => 'trim'],
            'emailRequired' => ['email', 'required'],
            'emailPattern' => ['email', 'email'],
            'passwordRequired' => ['password', 'required'],
            'passwordLength' => ['password', 'string', 'max' => 72, 'min' => 6],
        ];
    }
}
