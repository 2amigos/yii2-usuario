<?php
namespace Da\User\Model;

use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
use Yii;

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
 * @property string $password_hash
 * @property string $auth_key
 * @property integer $registration_ip
 * @property integer $confirmed_at
 * @property integer $blocked_at
 * @property integer $created_at
 * @property integer $updated_at
 *
 * Defined relations:
 * @property Account[] $accounts
 * @property Profile $profile
 */
class User extends ActiveRecord implements IdentityInterface
{
    /** @var string Plain password. Used for model validation. */
    public $password;

    /**
     * @return bool whether is blocked or not.
     */
    public function getIsBlocked()
    {
        return $this->blocked_at !== null;
    }

    public function getIsAdmin()
    {

    }

    public function hasRole($role)
    {

    }
}
