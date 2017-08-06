<?php

/**
 * @var Codeception\Scenario
 */

use Da\User\Model\Token;
use Da\User\Model\User;
use tests\_fixtures\TokenFixture;
use tests\_fixtures\UserFixture;
use yii\helpers\Html;

$I = new FunctionalTester($scenario);
$I->wantTo('ensure that password recovery works');
$I->haveFixtures(['user' => UserFixture::className(), 'token' => TokenFixture::className()]);

$I->amGoingTo('try to request recovery token for unconfirmed account');
$I->amOnRoute('/user/recovery/request');
$user = $I->grabFixture('user', 'unconfirmed');
$I->fillField('#recoveryform-email', $user->email);
$I->click('Continue');

$I->see('An email has been sent with instructions for resetting your password');

$I->amGoingTo('try to request recovery token');
$I->amOnRoute('/user/recovery/request');
$user = $I->grabFixture('user', 'user');
$I->fillField('#recoveryform-email', $user->email);
$I->click('Continue');

$I->see('An email has been sent with instructions for resetting your password');
$user = $I->grabRecord(User::className(), ['email' => $user->email]);
$token = $I->grabRecord(Token::className(), ['user_id' => $user->id, 'type' => Token::TYPE_RECOVERY]);
/** @var yii\swiftmailer\Message $message */
$message = $I->grabLastSentEmail();
$I->assertArrayHasKey($user->email, $message->getTo());
$I->assertContains(
    Html::encode($token->getUrl()),
    utf8_encode(quoted_printable_decode($message->getSwiftMessage()->toString()))
);

$I->amGoingTo('reset password with invalid token');
$user = $I->grabFixture('user', 'user_with_expired_recovery_token');
$token = $I->grabRecord(Token::className(), ['user_id' => $user->id, 'type' => Token::TYPE_RECOVERY]);
$I->amOnRoute('/user/recovery/reset', ['id' => $user->id, 'code' => $token->code]);
$I->see('Recovery link is invalid or expired. Please try requesting a new one.');

$I->amGoingTo('reset password');
$user = $I->grabFixture('user', 'user_with_recovery_token');
$token = $I->grabRecord(Token::className(), ['user_id' => $user->id, 'type' => Token::TYPE_RECOVERY]);
$I->amOnRoute('/user/recovery/reset', ['id' => $user->id, 'code' => $token->code]);
$I->fillField('#recoveryform-password', 'newpassword');
$I->click('Finish');

$I->amGoingTo('Login with old password');
$I->amOnRoute('/user/security/login');
$I->fillField('#loginform-login', $user->email);
$I->fillField('#loginform-password', 'qwerty');
$I->click('Sign in');
$I->see('Invalid login or password');

$I->amGoingTo('Login with new password');
$I->fillField('#loginform-login', $user->email);
$I->fillField('#loginform-password', 'newpassword');
$I->click('Sign in');
$I->see('Logout');
