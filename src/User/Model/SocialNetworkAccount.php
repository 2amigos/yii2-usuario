<?php

/*
 * This file is part of the 2amigos/yii2-usuario project.
 *
 * (c) 2amigOS! <http://2amigos.us/>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Da\User\Model;

use Da\User\Query\SocialNetworkAccountQuery;
use Da\User\Traits\ContainerAwareTrait;
use Da\User\Traits\ModuleAwareTrait;
use Yii;
use yii\base\Exception;
use yii\base\InvalidParamException;
use yii\db\ActiveRecord;
use yii\helpers\Url;

/**
 * /**
 * @property int $id          Id
 * @property int $user_id     User id, null if account is not bind to user
 * @property string $provider     Name of service
 * @property string $client_id    Account id
 * @property string $data         Account properties returned by social network (json encoded)
 * @property string $decodedData  Json-decoded properties
 * @property string $code
 * @property string $email
 * @property string $username
 * @property int $created_at
 * @property User $user        User that this account is connected for
 */
class SocialNetworkAccount extends ActiveRecord
{
    use ModuleAwareTrait;
    use ContainerAwareTrait;

    /**
     * @var array json decoded properties
     */
    protected $decodedData;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%social_account}}';
    }

    /**
     * @return bool Whether this social account is connected to user
     */
    public function getIsConnected()
    {
        return null !== $this->user_id;
    }

    /**
     * @return array json decoded properties
     */
    public function getDecodedData()
    {
        if ($this->data !== null && $this->decodedData === null) {
            $this->decodedData = json_decode($this->data);
        }

        return $this->decodedData;
    }

    /**
     * @throws Exception
     * @throws InvalidParamException
     * @return string                the connection url
     */
    public function getConnectionUrl()
    {
        $code = Yii::$app->security->generateRandomString();
        $this->updateAttributes(['code' => md5($code)]);

        return Url::to(['/user/registration/connect', 'code' => $code]);
    }

    /**
     * Connects account to a user.
     *
     * @param User $user
     *
     * @return int
     */
    public function connect(User $user)
    {
        return $this->updateAttributes(
            [
                'username' => null,
                'email' => null,
                'code' => null,
                'user_id' => $user->id,
            ]
        );
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne($this->getClassMap()->get(User::class), ['id' => 'user_id']);
    }

    /**
     * @return SocialNetworkAccountQuery
     */
    public static function find()
    {
        return new SocialNetworkAccountQuery(static::class);
    }
}
