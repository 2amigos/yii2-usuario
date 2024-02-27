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

use Da\User\Module;
use Da\User\Query\SessionHistoryQuery;
use Da\User\Traits\ModuleAwareTrait;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * @property int    $user_id
 * @property string $session_id
 * @property string $user_agent
 * @property string $ip
 * @property int    $created_at
 * @property int    $updated_at
 *
 * @property User $user
 * @property bool $isActive
 *
 * Dependencies:
 * @property-read Module $module
 */
class SessionHistory extends ActiveRecord
{
    use ModuleAwareTrait;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%session_history}}';
    }

    /** @inheritdoc */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'updatedAtAttribute' => false,
            ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'user_id' => Yii::t('usuario', 'User ID'),
            'session_id' => Yii::t('usuario', 'Session ID'),
            'user_agent' => Yii::t('usuario', 'User agent'),
            'ip' => Yii::t('usuario', 'IP'),
            'created_at' => Yii::t('usuario', 'Created at'),
            'updated_at' => Yii::t('usuario', 'Last activity'),
        ];
    }

    /**
     * @return bool Whether the session is an active or not.
     */
    public function getIsActive()
    {
        return isset($this->session_id) && $this->updated_at + $this->getModule()->rememberLoginLifespan > time();
    }

    /**
     * @return ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne($this->module->classMap['User'], ['id' => 'user_id']);
    }

    /** @inheritdoc */
    public function beforeSave($insert)
    {
        if ($insert && empty($this->session_id)) {
            $this->setAttribute('session_id', Yii::$app->session->getId());
        }

        return parent::beforeSave($insert);
    }

    /** @inheritdoc */
    public static function primaryKey()
    {
        return ['user_id', 'session_id'];
    }

    /**
     * @return SessionHistoryQuery
     */
    public static function find()
    {
        return new SessionHistoryQuery(static::class);
    }
}
