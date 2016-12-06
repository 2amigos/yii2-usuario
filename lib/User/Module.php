<?php

namespace Da\User;

use Da\User\Strategy\DefaultEmailChangeStrategy;

class Module extends \yii\base\Module
{
    /**
     * @var bool whether to allow registration process or not.
     */
    public $enableRegistration = true;
    /**
     * @var bool whether to force email confirmation to.
     */
    public $enableEmailConfirmation = true;
    /**
     * @var bool whether to generate passwords automatically and remove the password field from the registration form.
     */
    public $generatePasswords = false;
    /**
     * @var bool whether to allow login accounts with unconfirmed emails.
     */
    public $allowUnconfirmedEmailLogin = false;
    /**
     * @var bool whether to enable password recovery or not.
     */
    public $allowPasswordRecovery = true;
    /**
     * @var string the class name of the strategy class to handle user's email change.
     */
    public $emailChangeStrategy = DefaultEmailChangeStrategy::class;
    /**
     * @var int the time user will be auto logged in.
     */
    public $rememberLoginLifespan = 1209600;
    /**
     * @var int the time before the confirmation token becomes invalid. Defaults to 24 hours.
     */
    public $tokenConfirmationLifespan = 86400;
    /**
     * @var int the time before a recovery token is invalid. Defaults to 6 hours.
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
     * The higher the value of cost,
     * the longer it takes to generate the hash and to verify a password against it. Higher cost
     * therefore slows down a brute-force attack. For best protection against brute-force attacks,
     * set it to the highest value that is tolerable on production servers. The time taken to
     * compute the hash doubles for every increment by one of $cost.
     */
    public $blowfishCost = 10;
    /**
     * @var array the class map. How the container should load specific classes.
     */
    public $classMap = [];

    /**
     * @var array the url rules (routes)
     */
    public $routes = [
        '<id:\d+>' => 'profile/show',
        '<action:(login|logout)>' => 'auth/<action>',
        '<action:(register|resend)>' => 'registration/<action>',
        'confirm/<id:\d+>/<code:[A-Za-z0-9_-]+>' => 'registration/confirm',
        'forgot' => 'recovery/request',
        'recover/<id:\d+>/<code:[A-Za-z0-9_-]+>' => 'recovery/reset',
        'settings/<action:\w+>' => 'settings/<action>'
    ];
}
