<?php

/*
 * This file is part of the 2amigos/yii2-usuario project.
 *
 * (c) 2amigOS! <http://2amigos.us/>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Da\User\AuthClient;

use Da\User\Contracts\AuthClientInterface;
use Da\User\Traits\AuthClientUserIdTrait;
use yii\authclient\OAuth2;

/**
 * Microsoft365 allows authentication via Microsoft365 OAuth2 flow.
 * Before using Microsoft365 OAuth2 you must register your Microsoft Azure Application
 * @see https://portal.azure.com
 *
 * Note: the registered App must have the following:
 * -Authentication: 'Redirect URIs' set 'user/security/auth?authclient=microsoft365' as an absolute URL
 *  e.g. https://domain.com/index.php/user/security/auth?authclient=microsoft365
 * -API Permissions: 'Microsoft Graph' > 'User.Read'
 * -Decide whether the App should be
 *   single-tenant (only allow one Company to use it),
 *   multi-tenant (also allow other Companies to use it),
 *   personal account (allow accounts like xbox, skype etc. to use it)
 *   or both "multi-tenant and personal-account"
 * -In the Microsoft world even the Authorization URls are different dendinding if you allow single/multi/personal accounts.
 *  This client supports them: just set up the 'signInAudience' property (value for this it's in the manifest of your Azure App)
 *  accordingly to your needs; it defaults to the widest permissions available "AzureADandPersonalMicrosoftAccount"
 *  (details: https://learn.microsoft.com/en-us/entra/identity-platform/supported-accounts-validation)
 *
 * Example application configuration:
 *
 * ```
 * 'components' => [
 *     ...
 *     'authClientCollection' => [
 *         'class' => 'yii\authclient\Collection',
 *         'clients' => [
 *             'microsoft365' => [
 *                 'class' => 'yii\authclient\clients\Microsoft365',
 *                 'clientId' => 'a5e19acd-dc50-4b0a-864a-d13b9347ddf9',
 *                 'clientSecret' => 'ljSAd89.lvk34NV-3t4v3_2kl_42Rt4klr234',
 *                 'signInAudience' => 'AzureADandPersonalMicrosoftAccount',
 *             ],
 *         ],
 *     ]
 *     ...
 * ]
 * ```
 */
class Microsoft365 extends OAuth2 implements AuthClientInterface
{
    use AuthClientUserIdTrait;

    public const ACCOUNT_TYPE_SINGLETENANT = 'AzureADMyOrg'; // Accounts in this organizational directory only (Single tenant)
    public const ACCOUNT_TYPE_MULTITENANT = 'AzureADMultipleOrgs'; // Accounts in any organizational directory (Any Microsoft Entra directory - Multitenant)
    public const ACCOUNT_TYPE_MULTITENANTANDPERSONAL = 'AzureADandPersonalMicrosoftAccount'; // Accounts in any organizational directory (Any Microsoft Entra directory - Multitenant) and personal Microsoft accounts (such as Skype, Xbox)
    public const ACCOUNT_TYPE_PERSONAL = 'PersonalMicrosoftAccount'; // Personal Microsoft accounts only

    /**
     * @var string Micrososft365 Graph API endpoint.
     */
    public $apiBaseUrl = 'https://graph.microsoft.com/v1.0';

    /**
     * @var string 'signInAudience' in Microsoft Azure App manifest
     */
    public $signInAudience;

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();

        if (is_null($this->scope)) {
            $this->scope = 'User.Read';
        }

        if (is_null($this->signInAudience)) {
            $this->signInAudience = self::ACCOUNT_TYPE_MULTITENANTANDPERSONAL;
        }

        // In the Microsoft world Authorization URls are different if you use single-tenant or multi-tenant (@see https://learn.microsoft.com/en-us/entra/identity-platform/supported-accounts-validation)
        // This OAuth2 client supports also these scenarios: just set up 'signInAudience' accordingly to your needs. It defaults to the widest "AzureADandPersonalMicrosoftAccount"
        switch ($this->signInAudience) {
            case self::ACCOUNT_TYPE_SINGLETENANT:
                $this->authUrl  = 'https://login.microsoftonline.com/organizations/oauth2/v2.0/authorize';
                $this->tokenUrl = 'https://login.microsoftonline.com/organizations/oauth2/v2.0/token';
                break;
            case self::ACCOUNT_TYPE_PERSONAL:
                $this->authUrl  = 'https://login.microsoftonline.com/consumers/oauth2/v2.0/authorize';
                $this->tokenUrl = 'https://login.microsoftonline.com/consumers/oauth2/v2.0/token';
                break;
            case self::ACCOUNT_TYPE_MULTITENANT:
            case self::ACCOUNT_TYPE_MULTITENANTANDPERSONAL:
            default:
                $this->authUrl  = 'https://login.microsoftonline.com/common/oauth2/v2.0/authorize';
                $this->tokenUrl = 'https://login.microsoftonline.com/common/oauth2/v2.0/token';
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function initUserAttributes()
    {
        return $this->api('me', 'GET');
    }

    /**
     * {@inheritdoc}
     */
    public function applyAccessTokenToRequest($request, $accessToken)
    {
        $request->headers->set('Authorization', 'Bearer '.$accessToken->getToken());
    }

    /**
     * {@inheritdoc}
     */
    protected function defaultName()
    {
        return 'microsoft365';
    }

    /**
     * {@inheritdoc}
     */
    protected function defaultTitle()
    {
        return 'Microsoft 365';
    }

    /**
     * {@inheritdoc}
     */
    public function getEmail()
    {
        return $this->getUserAttributes()['mail'];
    }

    /**
     * {@inheritdoc}
     */
    public function getUsername()
    {
        return $this->getUserAttributes()['userPrincipalName'];
    }

}
