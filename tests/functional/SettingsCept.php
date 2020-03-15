<?php

/**
 * @var Codeception\Scenario
 */

use Da\User\Model\Token;
use Da\User\Model\User;
use tests\_fixtures\ProfileFixture;
use tests\_fixtures\UserFixture;
use yii\helpers\Html;

$I = new FunctionalTester($scenario);
$I->wantTo('ensure that account settings page work');
$I->haveFixtures(['user' => UserFixture::className(), 'profile' => ProfileFixture::className()]);

$user = $I->grabFixture('user', 'user');
$I->amLoggedInAs($user);

$I->amOnRoute('/user/settings/account');

$I->amGoingTo('check that current password is required and must be valid');
$I->fillField('#settingsform-email', $user->email);
$I->fillField('#settingsform-username', $user->username);
$I->fillField('#settingsform-current_password', 'wrong');
$I->click('Save');
$I->see('Current password is not valid');

$I->amGoingTo('check that email is changing properly');
$I->fillField('#settingsform-email', 'new_user@example.com');
$I->fillField('#settingsform-username', $user->username);
$I->fillField('#settingsform-current_password', 'qwerty');
$I->click('Save');
$I->seeRecord(User::className(), ['email' => $user->email, 'unconfirmed_email' => 'new_user@example.com']);

$I->see('A confirmation message has been sent to your new email address');
$user = $I->grabRecord(User::className(), ['id' => $user->id]);
$token = $I->grabRecord(Token::className(), ['user_id' => $user->id, 'type' => Token::TYPE_CONFIRM_NEW_EMAIL]);
/** @var yii\swiftmailer\Message $message */
$message = $I->grabLastSentEmail();
$I->assertArrayHasKey($user->unconfirmed_email, $message->getTo());
$I->assertStringContainsString(Html::encode($token->getUrl()), utf8_encode(quoted_printable_decode($message->getSwiftMessage()->toString())));
Yii::$app->user->logout();

$I->amGoingTo('log in using new email address before clicking the confirmation link');
$I->amOnRoute('/user/security/login');
$I->fillField('#loginform-login', 'new_user@example.com');
$I->fillField('#loginform-password', 'qwerty');
$I->click('Sign in');
$I->see('Invalid login or password');

$I->amGoingTo('log in using new email address after clicking the confirmation link');

$emailChangeService = Yii::createObject(\Da\User\Service\EmailChangeService::class, [$token->code, $user]);
$emailChangeService->run();

$I->fillField('#loginform-login', 'new_user@example.com');
$I->fillField('#loginform-password', 'qwerty');
$I->click('Sign in');
$I->see('Logout');
$I->seeRecord(User::className(), [
    'id' => 1,
    'email' => 'new_user@example.com',
    'unconfirmed_email' => null,
]);

$I->amGoingTo('reset email changing process');
$I->amOnRoute('/user/settings/account');

$I->fillField('#settingsform-email', 'user@example.com');
$I->fillField('#settingsform-username', $user->username);
$I->fillField('#settingsform-new_password', null);
$I->fillField('#settingsform-current_password', 'qwerty');
$I->click('Save');
$I->see('A confirmation message has been sent to your new email address');
$I->seeRecord(User::className(), [
    'id' => 1,
    'email' => 'new_user@example.com',
    'unconfirmed_email' => 'user@example.com',
]);

$I->fillField('#settingsform-email', 'new_user@example.com');
$I->fillField('#settingsform-username', $user->username);
$I->fillField('#settingsform-new_password', null);
$I->fillField('#settingsform-current_password', 'qwerty');
$I->click('Save');

$I->see('Your account details have been updated');
$I->seeRecord(User::className(), [
    'id' => 1,
    'email' => 'new_user@example.com',
    'unconfirmed_email' => null,
]);
$I->amGoingTo('change username and password');

$I->fillField('#settingsform-email', 'new_user@example.com');
$I->fillField('#settingsform-username', 'nickname');
$I->fillField('#settingsform-new_password', '123654');
$I->fillField('#settingsform-current_password', 'qwerty');
$I->click('Save');
$I->see('Your account details have been updated');
$I->seeRecord(User::className(), [
    'username' => 'nickname',
    'email' => 'new_user@example.com',
]);

Yii::$app->user->logout();

$I->amGoingTo('login with new credentials');
$I->amOnRoute('/user/security/login');
$I->fillField('#loginform-login', 'new_user@example.com');
$I->fillField('#loginform-password', '123654');
$I->click('Sign in');
$I->see('Logout');
