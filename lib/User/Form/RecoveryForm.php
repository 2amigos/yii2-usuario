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

use Da\User\Query\UserQuery;
use Da\User\Traits\ContainerAwareTrait;
use Yii;
use yii\base\Model;

class RecoveryForm extends Model
{
    use ContainerAwareTrait;

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
            'email' => Yii::t('usuario', 'Email'),
            'password' => Yii::t('usuario', 'Password'),
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
