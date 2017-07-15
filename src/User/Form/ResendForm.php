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
     * @param array     $config
     */
    public function __construct(UserQuery $userQuery, $config = [])
    {
        $this->userQuery = $userQuery;
        parent::__construct($config);
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            'emailRequired' => ['email', 'required'],
            'emailPattern' => ['email', 'email'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'email' => Yii::t('usuario', 'Email'),
        ];
    }
}
