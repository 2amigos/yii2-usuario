<?php
namespace Da\User\Model;

use Da\User\Helper\SecurityHelper;
use Da\User\Query\UserQuery;
use Da\User\Traits\ContainerTrait;
use Da\User\Traits\ModuleTrait;
use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\web\Application;
use yii\web\IdentityInterface;

/**
 * User ActiveRecord model.
 *
 * @property bool $isAdmin
 * @property bool $isBlocked
 * @property bool $isConfirmed
 *
 * Database fields:
 * @property integer $id
 * @property string $username
 * @property string $email
 * @property string $unconfirmed_email
 * @property string $password_hash
 * @property string $auth_key
 * @property integer $registration_ip
 * @property integer $confirmed_at
 * @property integer $blocked_at
 * @property integer $flags
 * @property integer $created_at
 * @property integer $updated_at
 *
 * Defined relations:
 * @property SocialNetworkAccount[] $socialNetworkAccounts
 * @property Profile $profile
 */
class User extends ActiveRecord implements IdentityInterface
{
    use ModuleTrait;
    use ContainerTrait;

    // following constants are used on secured email changing process
    const OLD_EMAIL_CONFIRMED = 0b1;
    const NEW_EMAIL_CONFIRMED = 0b10;

    /**
     * @var string Plain password. Used for model validation.
     */
    public $password;
    /**
     * @var array connected account list
     */
    protected $connectedAccounts;

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        /** @var SecurityHelper $security */
        $security = $this->make(SecurityHelper::class);
        if ($insert) {
            $this->setAttribute('auth_key', $security->generateRandomString());
            if (Yii::$app instanceof Application) {
                $this->setAttribute('registration_ip', Yii::$app->request->getUserIP());
            }
        }

        if (!empty($this->password)) {
            $this->setAttribute(
                'password_hash',
                $security->generatePasswordHash($this->password, $this->getModule()->blowfishCost)
            );
        }

        return parent::beforeSave($insert);
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'username' => Yii::t('user', 'Username'),
            'email' => Yii::t('user', 'Email'),
            'registration_ip' => Yii::t('user', 'Registration ip'),
            'unconfirmed_email' => Yii::t('user', 'New email'),
            'password' => Yii::t('user', 'Password'),
            'created_at' => Yii::t('user', 'Registration time'),
            'confirmed_at' => Yii::t('user', 'Confirmation time'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return ArrayHelper::merge(
            parent::scenarios(),
            [
                'register' => ['username', 'email', 'password'],
                'connect' => ['username', 'email'],
                'create' => ['username', 'email', 'password'],
                'update' => ['username', 'email', 'password'],
                'settings' => ['username', 'email', 'password'],
            ]
        );
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // username rules
            'usernameRequired' => ['username', 'required', 'on' => ['register', 'create', 'connect', 'update']],
            'usernameMatch' => ['username', 'match', 'pattern' => '/^[-a-zA-Z0-9_\.@]+$/'],
            'usernameLength' => ['username', 'string', 'min' => 3, 'max' => 255],
            'usernameTrim' => ['username', 'trim'],
            'usernameUnique' => [
                'username',
                'unique',
                'message' => Yii::t('user', 'This username has already been taken')
            ],

            // email rules
            'emailRequired' => ['email', 'required', 'on' => ['register', 'connect', 'create', 'update']],
            'emailPattern' => ['email', 'email'],
            'emailLength' => ['email', 'string', 'max' => 255],
            'emailUnique' => [
                'email',
                'unique',
                'message' => Yii::t('user', 'This email address has already been taken')
            ],
            'emailTrim' => ['email', 'trim'],

            // password rules
            'passwordRequired' => ['password', 'required', 'on' => ['register']],
            'passwordLength' => ['password', 'string', 'min' => 6, 'max' => 72, 'on' => ['register', 'create']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAttribute('auth_key') === $authKey;
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getAttribute('id');
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->getAttribute('auth_key');
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    /**
     * @return bool whether is blocked or not.
     */
    public function getIsBlocked()
    {
        return $this->blocked_at !== null;
    }

    /**
     * @return bool whether the user is an admin or not
     */
    public function getIsAdmin()
    {
        return $this->getAuth()->isAdmin($this->username);
    }

    /**
     * @return bool
     */
    public function getIsConfirmed()
    {
        return $this->confirmed_at !== null;
    }

    /**
     * Checks whether a user has a specific role
     *
     * @param string $role
     *
     * @return bool
     */
    public function hasRole($role)
    {
        return $this->getAuth()->hasRole($this->id, $role);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProfile()
    {
        return $this->hasOne($this->getClassMap()->get('Profile'), ['user_id' => 'id']);
    }

    /**
     * @return SocialNetworkAccount[] social connected accounts [ 'providerName' => socialAccountModel ]
     */
    public function getSocialNetworkAccounts()
    {
        if ($this->connectedAccounts == null) {
            /** @var SocialNetworkAccount[] $accounts */
            $accounts = $this->hasMany($this->getClassMap()
                ->get(SocialNetworkAccount::class), ['user_id' => 'id'])
                ->all();

            foreach ($accounts as $account) {
                $this->connectedAccounts[$account->provider] = $account;
            }
        }

        return $this->connectedAccounts;
    }

    /**
     * @return UserQuery
     */
    public static function find()
    {
        return new UserQuery(static::class);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('Method "' . __CLASS__ . '::' . __METHOD__ . '" is not implemented.');
    }
}
