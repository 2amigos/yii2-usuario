<?php


use Da\User\Model\Token;
use Da\User\Model\User;
use Da\User\Module;
use tests\_fixtures\UserFixture;
use tests\_fixtures\TokenFixture;
use yii\helpers\Html;

class RegistrationCest
{
    public function _before(FunctionalTester $I)
    {
        $I->haveFixtures(['user' => UserFixture::class]);
    }

    public function _after(FunctionalTester $I)
    {
        Yii::$app->getModule('user')->enableEmailConfirmation = true;
        Yii::$app->getModule('user')->generatePasswords = true;
    }

    /**
     * Tests registration with email, username and password without any confirmation.
     *
     * @param FunctionalTester $I
     */
    public function testRegistration(FunctionalTester $I)
    {
        Yii::$app->getModule('user')->enableEmailConfirmation = false;
        Yii::$app->getModule('user')->generatePasswords = false;

        $I->amOnRoute('/user/registration/register');

        $I->amGoingTo('try to register with empty credentials');
        $this->register($I, '', '', '');
        $I->see('Username cannot be blank');
        $I->see('Email cannot be blank');
        $I->see('Password cannot be blank');

        $I->amGoingTo('try to register with already used email and username');
        $user = $I->grabFixture('user', 'user');

        $this->register($I, $user->email, $user->username, 'qwerty');
        $I->see(Html::encode('This username has already been taken'));
        $I->see(Html::encode('This email address has already been taken'));

        $this->register($I, 'tester@example.com', 'tester', 'tester');
        $I->see('Your account has been created');
        $user = $I->grabRecord(User::class, ['email' => 'tester@example.com']);
        $I->assertTrue($user->isConfirmed);

        $I->amOnRoute('/user/security/login');
        $I->fillField('#loginform-login', 'tester');
        $I->fillField('#loginform-password', 'tester');
        $I->click('Sign in');
        $I->see('Logout');
    }

    /**
     * Tests registration when confirmation message is sent.
     *
     * @param FunctionalTester $I
     */
    public function testRegistrationWithConfirmation(FunctionalTester $I)
    {
        Yii::$app->getModule('user')->enableEmailConfirmation = true;
        $I->amOnRoute('/user/registration/register');
        $this->register($I, 'tester@example.com', 'tester', 'tester');
        $I->see('Your account has been created and a message with further instructions has been sent to your email');
        $user = $I->grabRecord(User::class, ['email' => 'tester@example.com']);
        $token = $I->grabRecord(Token::class, ['user_id' => $user->id, 'type' => Token::TYPE_CONFIRMATION]);
        /** @var \yii\mail\MessageInterface $message */
        $message = $I->grabLastSentEmail();
        $I->assertArrayHasKey($user->email, $message->getTo());
        $I->assertStringContainsString(Html::encode($token->getUrl()), utf8_encode(quoted_printable_decode($message->toString())));
        $I->assertFalse($user->isConfirmed);
    }

    /**
     * Tests registration when user should set the password right after confirmation
     *
     * @param FunctionalTester $I
     */
    public function testRegistrationWithoutPassword(FunctionalTester $I)
    {
        /** @var Module $module */
        $module = Yii::$app->getModule('user');
        $module->enableEmailConfirmation = false;
        $module->generatePasswords = true;
        $I->amOnRoute('/user/registration/register');
        $this->register($I, 'tester@example.com', 'tester');
        $I->see('Your account has been created');
        $user = $I->grabRecord(User::class, ['email' => 'tester@example.com']);
        $I->assertEquals('tester', $user->username);
        /** @var \yii\mail\MessageInterface $message */
        $message = $I->grabLastSentEmail();
        $I->assertArrayHasKey($user->email, $message->getTo());
        $I->assertStringContainsString('We have generated a password for you', utf8_encode(quoted_printable_decode($message->toString())));
    }

    /**
     * Tests registration when password is generated automatically and sent to user.
     *
     * @param FunctionalTester $I
     */
    public function testRegistrationWithPasswordResetAfterConfirmation(FunctionalTester $I)
    {
        /** @var Module $module */
        $module = Yii::$app->getModule('user');
        $module->generatePasswords = false;
        $module->offerPasswordChangeAfterConfirmation = true;
        $I->amOnRoute('/user/registration/register');
        $I->dontSee('Password');
        $this->register($I, 'tester@example.com', 'tester');
        $I->see('Your account has been created');
        /** @var User $user */
        $user = $I->grabRecord(User::class, ['email' => 'tester@example.com']);
        $I->assertEquals('tester', $user->username);
        /** @var \yii\mail\MessageInterface $message */
        $message = $I->grabLastSentEmail();
        $I->assertArrayHasKey($user->email, $message->getTo());
        $I->assertStringNotContainsString('We have generated a password for you', utf8_encode(quoted_printable_decode($message->toString())));
        /** @var \Da\User\Query\TokenQuery $tokenQuery */
        $tokenQuery = Yii::createObject(\Da\User\Query\TokenQuery::class);
        /** @var Token $confirmationToken */
        $confirmationToken = $tokenQuery->whereUserId($user->primaryKey)->one();
        $I->amOnPage($confirmationToken->getUrl());
        $I->see("Thank you, registration is now complete.");
        $I->see("Reset your password");

    }

    protected function register(FunctionalTester $I, $email, $username = null, $password = null) {
        $I->fillField('#registrationform-email', $email);
        $I->fillField('#registrationform-username', $username);
        if ($password !== null) {
            $I->fillField('#registrationform-password', $password);
        }
        $I->click('Sign up');

    }
}
