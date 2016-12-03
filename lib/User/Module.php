<?php
namespace Da\User;

/**
 *
 * Module.php
 *
 * Date: 3/12/16
 * Time: 15:15
 * @author Antonio Ramirez <hola@2amigos.us>
 */
class Module extends \yii\base\Module
{
    public $token
    /**
     * @var int the time before a recovery token is invalid. Defaults to 6 hours.
     */
    public $tokenRecoveryWithin = 21600;
    /**
     * @var array a list of admin usernames
     */
    public $administrators = [];
    /**
     * @var string the administrator permission name
     */
    public $administratorPermissionName;
    /**
     * @var array the class map used by the module.
     *
     * @see Bootstrap
     */
    public $classmap = [];
    /**
     * @var string the route prefix
     */
    public $prefix = 'user';

    /**
     * @var array the url rules (routes)
     */
    public $routes = [
        '<id:\d+>'                               => 'profile/show',
        '<action:(login|logout)>'                => 'security/<action>',
        '<action:(register|resend)>'             => 'registration/<action>',
        'confirm/<id:\d+>/<code:[A-Za-z0-9_-]+>' => 'registration/confirm',
        'forgot'                                 => 'recovery/request',
        'recover/<id:\d+>/<code:[A-Za-z0-9_-]+>' => 'recovery/reset',
        'settings/<action:\w+>'                  => 'settings/<action>'
    ];
}
