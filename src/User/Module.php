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
use Da\User\Filter\AccessRuleFilter;
use Yii;
use yii\base\Module as BaseModule;
use yii\helpers\Html;

/**
 * This is the main module class of the yii2-usuario extension.
 */
class Module extends BaseModule
{
    /**
     * @var bool whether to enable european G.D.P.R. compliance.
     *           This will add a few elements to comply with european general data protection regulation.
     *           This regulation affects to all companies in Europe a those companies outside that offer their
     *           services to the E.U.
     *           List of elements that will be added when this is enabled:
     *           - Checkbox to request consent on register form
     *           - Forgot me button in profile view.
     *           - Download my data button in profile
     */
    public $enableGdprCompliance = false;
    /**
     * @var null|array|string with the url to privacy policy.
     *                        Must be in the same format as yii/helpers/Url::to requires.
     */
    public $gdprPrivacyPolicyUrl = null;
    /**
     * @var array with the name of the user identity properties to be included when user request download of his data.
     *            Names can include relations like `profile.name`.
     *            GPDR says:
     *            > The data subject shall have the right to receive the personal data concerning him or her, which he
     *            > or she has provided to a controller, in a structured, commonly used and machine-readable format
     */
    public $gdprExportProperties = [
        'email',
        'username',
        'profile.public_email',
        'profile.name',
        'profile.gravatar_email',
        'profile.location',
        'profile.website',
        'profile.bio'
    ];
    /**
     * @var string prefix to be used as a replacement when user requests deletion of his data.
     */
    public $gdprAnonymizePrefix = 'GDPR';
    /**
     * @var bool if true, all registered users will be prompted to give consent if they have not gave it earlier.
     */
    public $gdprRequireConsentToAll = false;
    /**
     * @var null|string use this to customize the message that will appear as hint in the give consent checkbox
     */
    public $gdprConsentMessage;
    /**
     * @var array list of url that does not require explicit data processing consent
     *            to be accessed, like own profile, account... You can use wildcards like `route/to/*`. Do not prefix
     *            "/" required for redirection, they are used to match against action ids.
     *
     * @see AccessRuleFilter
     */
    public $gdprConsentExcludedUrls = [
        'user/settings/*'
    ];
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
     * @var bool whether to allow auto login or not
     */
    public $enableAutoLogin = true;
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
     * @var string Web controller namespace
     */
    public $controllerNamespace = 'Da\User\Controller';
    /**
     * @var string Console controller namespace
     */
    public $consoleControllerNamespace = 'Da\User\Command';
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
    /**
     * @var integer If != NULL sets a max password age in days
     */
    public $maxPasswordAge;
    /**
     * @var boolean whether to restrict assignment of permissions to users
     */
    public $restrictUserPermissionAssignment = false;

    /**
     * @return string with the hit to be used with the give consent checkbox
     */
    public function getConsentMessage()
    {
        $defaultConsentMessage = Yii::t(
            'usuario',
            'I agree processing of my personal data and the use of cookies to facilitate the operation of this site. For more information read our {privacyPolicy}',
            [
                'privacyPolicy' => Html::a(
                    Yii::t('usuario', 'privacy policy'),
                    $this->gdprPrivacyPolicyUrl,
                    ['target' => '_blank']
                ),
            ]
        );

        return $this->gdprConsentMessage ?: $defaultConsentMessage;
    }
}
