<?php

namespace tests\_pages;

use yii\codeception\BasePage;

/**
 * Represents login page.
 *
 * @property \FunctionalTester $actor
 */
class LoginPage extends BasePage
{
    /** @inheritdoc */
    public $route = '/user/security/login';

    /**
     * @param $login
     * @param $password
     */
    public function login($login, $password)
    {
        $this->actor->fillField('#loginform-login', $login);
        $this->actor->fillField('#loginform-password', $password);
        $this->actor->click('Sign in');
    }
}
