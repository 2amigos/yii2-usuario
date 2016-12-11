<?php

namespace tests\_pages;

use yii\codeception\BasePage;

/**
 * Represents resend page.
 *
 * @property \FunctionalTester $actor
 */
class ResendPage extends BasePage
{
    /** @inheritdoc */
    public $route = '/user/registration/resend';

    /**
     * @param $email
     */
    public function resend($email)
    {
        $this->actor->fillField('#resendform-email', $email);
        $this->actor->click('Continue');
    }
}
