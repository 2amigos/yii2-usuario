<?php

/*
 * This file is part of the 2amigos/yii2-usuario project.
 *
 * (c) 2amigOS! <http://2amigos.us/>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Da\User;

use Da\User\Contracts\MailChangeStrategyInterface;
use yii\base\Module as BaseModule;

/**
 * This is the main module class of the yii2-usuario extension.
 */
class Module extends BaseModule
{
    /**
     * @var bool whether to enable two factor authentication or not
     */
    public $enableTwoFactorAuthentication = false;
    /**
     * @var int cycles of key generation are set on 30 sec. To avoid sync issues, increased validity up to 60 sec.
     * @see http://2fa-library.readthedocs.io/en/latest/
     */
    public $twoFactorAuthenticationCycles = 1;
    /**
     * @var bool whether to allow registration process or not
     */
    public $enableRegistration = true;
    /**
     * @var bool whether to force email confirmation to
     */
    public $enableEmailConfirmation = true;
    /**
     * @var bool whether to display flash messages or not
     */
    public $enableFlashMessages = true;
    /**
     * @var bool whether to be able to, as an admin, impersonate other users
     */
    public $enableSwitchIdentities = true;
    /**
     * @var bool whether to generate passwords automatically and remove the password field from the registration form
     */
    public $generatePasswords = false;
    /**
     * @var bool whether to allow login accounts with unconfirmed emails
     */
    public $allowUnconfirmedEmailLogin = false;
    /**
     * @var bool whether to enable password recovery or not
     */
    public $allowPasswordRecovery = true;
    /**
     * @var bool whether to enable password recovery from the admin console
     */
    public $allowAdminPasswordRecovery = true;
    /**
     * @var bool whether user can remove his account
     */
    public $allowAccountDelete = false;
    /**
     * @var string the class name of the strategy class to handle user's email change
     */
    public $emailChangeStrategy = MailChangeStrategyInterface::TYPE_DEFAULT;
    /**
     * @var int the time user will be auto logged in
     */
    public $rememberLoginLifespan = 1209600;
    /**
     * @var int the time before the confirmation token becomes invalid. Defaults to 24 hours
     */
    public $tokenConfirmationLifespan = 86400;
    /**
     * @var int the time before a recovery token is invalid. Defaults to 6 hours
     */
    public $tokenRecoveryLifespan = 21600;
    /**
     * @var array a list of admin usernames
     */
    public $administrators = [];
    /**
     * @var string the administrator permission name
     */
    public $administratorPermissionName;
    /**
     * @var string the route prefix
     */
    public $prefix = 'user';
    /**
     * @var array MailService configuration
     */
    public $mailParams = [];
    /**
     * @var int the cost parameter used by the Blowfish hash algorithm.
     *          The higher the value of cost, the longer it takes to generate the hash and to verify a password
     *          against it. Higher cost therefore slows down a brute-force attack. For best protection against
     *          brute-force attacks, set it to the highest value that is tolerable on production servers. The time taken
     *          to compute the hash doubles for every increment by one of $cost
     */
    public $blowfishCost = 10;
    /**
     * @var array the class map. How the container should load specific classes
     * @see Bootstrap::buildClassMap() for more details
     */
    public $classMap = [];
    /**
     * @var array the url rules (routes)
     */
    public $routes = [
        '<id:\d+>' => 'profile/show',
        '<action:(login|logout)>' => 'security/<action>',
        '<action:(register|resend)>' => 'registration/<action>',
        'confirm/<id:\d+>/<code:[A-Za-z0-9_-]+>' => 'registration/confirm',
        'forgot' => 'recovery/request',
        'recover/<id:\d+>/<code:[A-Za-z0-9_-]+>' => 'recovery/reset'
    ];
    /**
     * @var string
     */
    public $viewPath = '@Da/User/resources/views';
    /**
     * @var string the session key name to impersonate users. Please, modify it for security reasons!
     */
    public $switchIdentitySessionKey = 'yuik_usuario';
}
