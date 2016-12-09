<?php
namespace Da\User\Form;

use Da\User\Query\UserQuery;
use Yii;
use yii\base\Model;

class ResendForm extends Model
{
    /**
     * @var string
     */
    public $email;
    /**
     * @var UserQuery
     */
    protected $userQuery;

    /**
     * @param UserQuery $userQuery
     * @param array  $config
     */
    public function __construct(UserQuery $userQuery, $config = [])
    {
        $this->userQuery = $userQuery;
        parent::__construct($config);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            'emailRequired' => ['email', 'required'],
            'emailPattern' => ['email', 'email'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'email' => Yii::t('user', 'Email'),
        ];
    }
}
