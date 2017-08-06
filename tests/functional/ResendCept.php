<?php

/**
 * @var Codeception\Scenario
 */

use tests\_fixtures\UserFixture;

$I = new FunctionalTester($scenario);
$I->wantTo('ensure that resending of confirmation tokens works');
$I->haveFixtures(['user' => UserFixture::className()]);

$I->amGoingTo('try to resend token to non-existent user');
$I->amOnRoute('/user/registration/resend');
$I->fillField('#resendform-email', 'foo@example.com');
$I->click('Continue');
$I->see('We couldn\'t re-send the mail to confirm your address. Please, verify is the correct email or if it has been confirmed already.');

$I->amGoingTo('try to resend token to already confirmed user');
$I->amOnRoute('/user/registration/resend');
$user = $I->grabFixture('user', 'user');
$I->fillField('#resendform-email', $user->email);
$I->click('Continue');
$I->see('We couldn\'t re-send the mail to confirm your address. Please, verify is the correct email or if it has been confirmed already.');

$I->amGoingTo('try to resend token to unconfirmed user');
$I->amOnRoute('/user/registration/resend');
$user = $I->grabFixture('user', 'unconfirmed');
$I->fillField('#resendform-email', $user->email);
$I->click('Continue');
$I->see('A message has been sent to your email address. It contains a confirmation link that you must click to complete registration.');
