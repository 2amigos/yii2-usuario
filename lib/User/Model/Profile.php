<?php
namespace Da\User\Model;

use Da\User\Helper\GravatarHelper;
use Da\User\Query\ProfileQuery;
use Da\User\Traits\ContainerTrait;
use Da\User\Traits\ModuleTrait;
use Da\User\Validator\TimeZoneValidator;
use Yii;
use yii\db\ActiveRecord;
use Exception;
use DateTimeZone;
use DateTime;

/**
 *
 * @property integer $user_id
 * @property string $name
 * @property string $public_email
 * @property string $gravatar_email
 * @property string $gravatar_id
 * @property string $location
 * @property string $website
 * @property string $bio
 * @property string $timezone
 *
 * @property User $user
 */
class Profile extends ActiveRecord
{
    use ModuleTrait;
    use ContainerTrait;

    /**
     * @inheritdoc
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
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%profile}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            'bioString' => ['bio', 'string'],
            'timeZoneValidation' => [
                'timezone',
                function ($attribute) {
                    if ($this->make(TimeZoneValidator::class, [$attribute])->validate()) {
                        $this->addError($attribute, Yii::t('user', 'Time zone is not valid'));
                    }
                }
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
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'name' => Yii::t('user', 'Name'),
            'public_email' => Yii::t('user', 'Email (public)'),
            'gravatar_email' => Yii::t('user', 'Gravatar email'),
            'location' => Yii::t('user', 'Location'),
            'website' => Yii::t('user', 'Website'),
            'bio' => Yii::t('user', 'Bio'),
            'timezone' => Yii::t('user', 'Time zone'),
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
     * Set the User's timezone
     *
     * @param DateTimeZone $timezone
     */
    public function setTimeZone(DateTimeZone $timezone)
    {
        $this->setAttribute('timezone', $timezone);
    }

    /**
     * Get User's local time
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
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne($this->getClassMap()->get('User'), ['id' => 'user_id']);
    }

    /**
     * @param int $size
     *
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
