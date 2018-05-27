<?php

/*
 * This file is part of the 2amigos/yii2-usuario project.
 *
 * (c) 2amigOS! <http://2amigos.us/>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Da\User\Search;

use Da\User\Query\UserQuery;
use Yii;
use yii\base\InvalidParamException;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class UserSearch extends Model
{
    /**
     * @var string
     */
    public $username;
    /**
     * @var string
     */
    public $email;
    /**
     * @var int
     */
    public $created_at;
    /**
     * @var int
     */
    public $last_login_at;
    /**
     * @var string
     */
    public $registration_ip;
    /**
     * @var string
     */
    public $last_login_ip;
    /**
     * @var UserQuery
     */
    protected $query;

    /**
     * UserSearch constructor.
     *
     * @param UserQuery $query
     * @param array     $config
     */
    public function __construct(UserQuery $query, $config = [])
    {
        $this->query = $query;
        parent::__construct($config);
    }

    /**
     * @return array
     */
    public function rules()
    {
        return [
            'safeFields' => [['username', 'email', 'registration_ip', 'created_at', 'last_login_at, last_login_ip'], 'safe'],
            'createdDefault' => [['created_at', 'last_login_at'], 'default', 'value' => null],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'username' => Yii::t('usuario', 'Username'),
            'email' => Yii::t('usuario', 'Email'),
            'created_at' => Yii::t('usuario', 'Registration time'),
            'registration_ip' => Yii::t('usuario', 'Registration IP'),
            'last_login_at' => Yii::t('usuario', 'Last login time'),
            'last_login_ip' => Yii::t('usuario', 'Last login IP'),
        ];
    }

    /**
     * @param $params
     *
     * @throws InvalidParamException
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = $this->query;

        $dataProvider = new ActiveDataProvider(
            [
                'query' => $query,
            ]
        );

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        if ($this->created_at !== null) {
            $date = strtotime($this->created_at);
            $query->andFilterWhere(['between', 'created_at', $date, $date + 3600 * 24]);
        }

        if ($this->last_login_at !== null) {
            $date = strtotime($this->last_login_at);
            $query->andFilterWhere(['between', 'last_login_at', $date, $date + 3600 * 24]);
        }

        $query
            ->andFilterWhere(['like', 'username', $this->username])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['registration_ip' => $this->registration_ip])
            ->andFilterWhere(['last_login_ip' => $this->last_login_ip]);

        return $dataProvider;
    }
}
