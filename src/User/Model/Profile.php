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

use Da\User\Helper\GravatarHelper;
use Da\User\Query\ProfileQuery;
use Da\User\Traits\ContainerAwareTrait;
use Da\User\Traits\ModuleAwareTrait;
use Da\User\Validator\TimeZoneValidator;
use DateTime;
use DateTimeZone;
use Exception;
use Yii;
use yii\base\InvalidConfigException;
use yii\base\InvalidParamException;
use yii\db\ActiveRecord;

/**
 * @property int $user_id
 * @property string $name
 * @property string $public_email
 * @property string $gravatar_email
 * @property string $gravatar_id
 * @property string $location
 * @property string $website
 * @property string $bio
 * @property string $timezone
 * @property User $user
 */
class Profile extends ActiveRecord
{
    use ModuleAwareTrait;
    use ContainerAwareTrait;

    /**
     * {@inheritdoc}
     *
     * @throws InvalidParamException
     * @throws InvalidConfigException
     */
    public function beforeSave($insert)
    {
        if ($this->isAttributeChanged('gravatar_email')) {
            $this->setAttribute(
                'gravatar_id',
                $this->make(GravatarHelper::class)->buildId(trim($this->getAttribute('gravatar_email')))
            );
        }

        return parent::beforeSave($insert);
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%profile}}';
    }

    /**
     * {@inheritdoc}
     *
     * @throws InvalidConfigException
     */
    public function rules()
    {
        return [
            'bioString' => ['bio', 'string'],
            'timeZoneValidation' => [
                'timezone',
                function ($attribute) {
                    if ($this->make(TimeZoneValidator::class, [$this->{$attribute}])->validate() === false) {
                        $this->addError($attribute, Yii::t('usuario', 'Time zone is not valid'));
                    }
                },
            ],
            'publicEmailPattern' => ['public_email', 'email'],
            'gravatarEmailPattern' => ['gravatar_email', 'email'],
            'websiteUrl' => ['website', 'url'],
            'nameLength' => ['name', 'string', 'max' => 255],
            'publicEmailLength' => ['public_email', 'string', 'max' => 255],
            'gravatarEmailLength' => ['gravatar_email', 'string', 'max' => 255],
            'locationLength' => ['location', 'string', 'max' => 255],
            'websiteLength' => ['website', 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'name' => Yii::t('usuario', 'Name'),
            'public_email' => Yii::t('usuario', 'Email (public)'),
            'gravatar_email' => Yii::t('usuario', 'Gravatar email'),
            'location' => Yii::t('usuario', 'Location'),
            'website' => Yii::t('usuario', 'Website'),
            'bio' => Yii::t('usuario', 'Bio'),
            'timezone' => Yii::t('usuario', 'Time zone'),
        ];
    }

    /**
     * Get the User's timezone.
     *
     * @return DateTimeZone
     */
    public function getTimeZone()
    {
        try {
            return new DateTimeZone($this->timezone);
        } catch (Exception $e) {
            return new DateTimeZone(Yii::$app->getTimeZone());
        }
    }

    /**
     * Set the User's timezone.
     *
     * @param DateTimeZone $timezone
     *
     * @throws InvalidParamException
     */
    public function setTimeZone(DateTimeZone $timezone)
    {
        $this->setAttribute('timezone', $timezone->getName());
    }

    /**
     * Get User's local time.
     *
     * @param DateTime|null $dateTime
     *
     * @return DateTime
     */
    public function getLocalTimeZone(DateTime $dateTime = null)
    {
        return $dateTime === null ? new DateTime() : $dateTime->setTimezone($this->getTimeZone());
    }

    /**
     * @throws InvalidConfigException
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne($this->getClassMap()->get(User::class), ['id' => 'user_id']);
    }

    /**
     * @param int $size
     *
     * @throws InvalidConfigException
     * @return mixed
     */
    public function getAvatarUrl($size = 200)
    {
        return $this->make(GravatarHelper::class)->getUrl($this->gravatar_id, $size);
    }

    /**
     * @return ProfileQuery
     */
    public static function find()
    {
        return new ProfileQuery(static::class);
    }
}
