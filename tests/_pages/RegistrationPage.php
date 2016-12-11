<?php

namespace tests\_pages;

use yii\codeception\BasePage;

/**
 * Represents registration page.
 *
 * @property \FunctionalTester $actor
 */
class RegistrationPage extends BasePage
{
    /** @inheritdoc */
    public $route = '/user/registration/register';

    /**
     * @param $username
     * @param $email
     * @param $password
     */
    public function register($email, $username = null, $password = null)
    {
        $this->actor->fillField('#registrationform-email', $email);
        $this->actor->fillField('#registrationform-username', $username);
        if ($password !== null) {
            $this->actor->fillField('#registrationform-password', $password);
        }
        $this->actor->click('Sign up');
    }
}
