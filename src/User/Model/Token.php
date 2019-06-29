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

use Da\User\Helper\SecurityHelper;
use Da\User\Query\TokenQuery;
use Da\User\Traits\ContainerAwareTrait;
use Da\User\Traits\ModuleAwareTrait;
use RuntimeException;
use yii\base\InvalidConfigException;
use yii\base\InvalidParamException;
use yii\db\ActiveRecord;
use yii\helpers\Url;

/**
 * Token Active Record model.
 *
 * @property int $user_id
 * @property string $code
 * @property int $type
 * @property string $url
 * @property bool $isExpired
 * @property int $created_at
 * @property User $user
 */
class Token extends ActiveRecord
{
    use ModuleAwareTrait;
    use ContainerAwareTrait;

    const TYPE_CONFIRMATION = 0;
    const TYPE_RECOVERY = 1;
    const TYPE_CONFIRM_NEW_EMAIL = 2;
    const TYPE_CONFIRM_OLD_EMAIL = 3;

    protected $routes = [
        self::TYPE_CONFIRMATION => '/user/registration/confirm',
        self::TYPE_RECOVERY => '/user/recovery/reset',
        self::TYPE_CONFIRM_NEW_EMAIL => '/user/settings/confirm',
        self::TYPE_CONFIRM_OLD_EMAIL => '/user/settings/confirm',
    ];

    /**
     * {@inheritdoc}
     *
     * @throws InvalidParamException
     * @throws InvalidConfigException
     */
    public function beforeSave($insert)
    {
        if ($insert) {
            $this->setAttribute('code', $this->make(SecurityHelper::class)->generateRandomString());
            static::deleteAll(['user_id' => $this->user_id, 'type' => $this->type]);
            $this->setAttribute('created_at', time());
        }

        return parent::beforeSave($insert);
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%token}}';
    }

    /**
     * {@inheritdoc}
     */
    public static function primaryKey()
    {
        return ['user_id', 'code', 'type'];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne($this->getClassMap()->get(User::class), ['id' => 'user_id']);
    }

    /**
     * @throws InvalidParamException
     * @return string
     */
    public function getUrl()
    {
        return Url::to([$this->routes[$this->type], 'id' => $this->user_id, 'code' => $this->code], true);
    }

    /**
     * @throws RuntimeException
     * @return bool             Whether token has expired
     */
    public function getIsExpired()
    {
        if ($this->type === static::TYPE_RECOVERY) {
            $expirationTime = $this->getModule()->tokenRecoveryLifespan;
        } elseif ($this->type >= static::TYPE_CONFIRMATION && $this->type <= static::TYPE_CONFIRM_OLD_EMAIL) {
            $expirationTime = $this->getModule()->tokenConfirmationLifespan;
        } else {
            throw new RuntimeException('Unknown Token type.');
        }

        return ($this->created_at + $expirationTime) < time();
    }

    /**
     * @return TokenQuery
     */
    public static function find()
    {
        return new TokenQuery(static::class);
    }
}
