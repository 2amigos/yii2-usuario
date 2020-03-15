<?php


use Da\User\Model\Token;
use Da\User\Model\User;
use Da\User\Module;
use tests\_fixtures\UserFixture;
use yii\helpers\Html;

class GdprCest
{
    public function _before(FunctionalTester $I)
    {
        $I->haveFixtures([
            'user' => UserFixture::class,
            'profile' => \tests\_fixtures\ProfileFixture::class
        ]);
    }

    public function _after(FunctionalTester $I)
    {
        \Yii::$container->set(Module::className(), [
            'enableEmailConfirmation' => true,
            'generatePasswords' => false,
        ]);
    }

    /**
     * Tests registration with email, username and password without any confirmation.
     *
     * @param FunctionalTester $I
     */
    public function testGdprRegistration(FunctionalTester $I)
    {

        $this->_prepareModule(false, false);

        $I->amOnRoute('/user/registration/register');

        $I->amGoingTo('try to register with empty credentials');
        $this->register($I, '', '', '', false);
        $I->see('Username cannot be blank');
        $I->see('Email cannot be blank');
        $I->see('Password cannot be blank');
        $I->see('Your consent is required to register');

        $I->amGoingTo('try to register with already used email and username');
        $user = $I->grabFixture('user', 'user');

        $this->register($I, $user->email, $user->username, 'qwerty');
        $I->see(Html::encode('This username has already been taken'));
        $I->see(Html::encode('This email address has already been taken'));
        $this->register($I, 'tester@example.com', 'tester', 'tester');
        $I->see('Your account has been created');
        $user = $I->grabRecord(User::className(), ['email' => 'tester@example.com']);
        $I->assertTrue($user->isConfirmed);

        $I->amOnRoute('/user/security/login');
        $I->fillField('#loginform-login', 'tester');
        $I->fillField('#loginform-password', 'tester');
        $I->click('Sign in');
        $I->see('Logout');
    }

    public function _prepareModule($emailConfirmation = true, $generatePasswords = false, $enableGdpr = true)
    {
        /* @var $module Module */
        $module = Yii::$app->getModule('user');
        $module->enableEmailConfirmation = $emailConfirmation;
        $module->generatePasswords = $generatePasswords;
        $module->enableGdprCompliance = $enableGdpr;
    }

    protected function register(FunctionalTester $I, $email, $username = null, $password = null, $gdpr_consent = true)
    {
        $I->fillField('#registrationform-email', $email);
        $I->fillField('#registrationform-username', $username);
        if ($password !== null) {
            $I->fillField('#registrationform-password', $password);
        }
        if ($gdpr_consent)
            $I->checkOption('#registrationform-gdpr_consent');

        $I->click('Sign up');

    }

    /**
     * Tests registration when confirmation message is sent.
     *
     * @param FunctionalTester $I
     */
    public function testRegistrationWithConfirmation(FunctionalTester $I)
    {
        $this->_prepareModule(true);

        $I->amOnRoute('/user/registration/register');
        $this->register($I, 'tester@example.com', 'tester', 'tester');
        $I->see('Your account has been created and a message with further instructions has been sent to your email');
        $user = $I->grabRecord(User::className(), ['email' => 'tester@example.com']);
        $token = $I->grabRecord(Token::className(), ['user_id' => $user->id, 'type' => Token::TYPE_CONFIRMATION]);
        /** @var yii\swiftmailer\Message $message */
        $message = $I->grabLastSentEmail();
        $I->assertArrayHasKey($user->email, $message->getTo());
        $I->assertStringContainsString(Html::encode($token->getUrl()), utf8_encode(quoted_printable_decode($message->getSwiftMessage()->toString())));
        $I->assertFalse($user->isConfirmed);
    }

    /**
     * Tests registration when password is generated automatically and sent to user.
     *
     * @param FunctionalTester $I
     */
    public function testRegistrationWithoutPassword(FunctionalTester $I)
    {
        $this->_prepareModule(false, true);

        $I->amOnRoute('/user/registration/register');
        $this->register($I, 'tester@example.com', 'tester');
        $I->see('Your account has been created');
        $user = $I->grabRecord(User::className(), ['email' => 'tester@example.com']);
        $I->assertEquals('tester', $user->username);
        /** @var yii\swiftmailer\Message $message */
        $message = $I->grabLastSentEmail();
        $I->assertArrayHasKey($user->email, $message->getTo());
        $I->assertStringContainsString('We have generated a password for you', utf8_encode(quoted_printable_decode($message->getSwiftMessage()->toString())));
    }


    /**
     * Test privacy page
     *
     * @param FunctionalTester $I
     */
    public function testPrivacyPage(FunctionalTester $I)
    {

        $I->amGoingTo('try that privacy page works');
        $I->amLoggedInAs(1);
        $this->_prepareModule(false, false);
        $I->amOnRoute('/user/settings/privacy');
        $I->see('Export my data', 'h3');
        $I->see('Delete my account', 'h3');
        $I->amOnRoute('/user/settings/gdpr-delete');
        $I->fillField('#gdprdeleteform-password', 'wrongpassword');
        $I->click('Delete');
        $I->see('Invalid password');
        $I->fillField('#gdprdeleteform-password', 'qwerty');
        $I->click('Delete');
        $I->see('Login');
    }

    /**
     * Test privacy page
     *
     * @param FunctionalTester $I
     */
    public function testPrivacyPageAccess(FunctionalTester $I)
    {

        $I->amGoingTo('Try that a user cant access to privacy if GDPR is not enabled');
        $this->_prepareModule(false, false,false);
        $I->amLoggedInAs(1);
        $I->amOnRoute('/user/settings/privacy');
        $I->seeResponseCodeIs(404);
        $I->amOnRoute('/user/settings/privacy');
        $I->see('Not Found');
    }

    public function testForcedConsentRequirement(FunctionalTester $I)
    {
        $this->_prepareModule(false,false);
        /** @var Module $module */
        $module = Yii::$app->getModule('user');
        $module->gdprRequireConsentToAll = true;
        $I->amGoingTo('Try to access a page without giving data processing consent');
        $I->amLoggedInAs(1);
        $I->amOnRoute('/site/index');
        $I->seeElement('.give-consent-panel');
        $I->checkOption('#dynamicmodel-gdpr_consent');
        $I->click('Submit');
        $I->see('Profile settings');
    }
}
