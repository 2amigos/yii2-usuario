<?php

/**
 * @var Codeception\Scenario
 */

use tests\_fixtures\UserFixture;

$I = new FunctionalTester($scenario);
$I->wantTo('ensure that login works');
$I->haveFixtures(['user' => UserFixture::className()]);
$I->amOnRoute('/user/security/login');

$I->amGoingTo('try to login with empty credentials');
$I->fillField('#loginform-login', '');
$I->fillField('#loginform-password', '');
$I->click('Sign in');
$I->expectTo('see validations errors');
$I->see('Login cannot be blank.');
$I->see('Password cannot be blank.');


$I->amGoingTo('try to login with unconfirmed account');
$user = $I->grabFixture('user', 'unconfirmed');
$I->fillField('#loginform-login', $user->email);
$I->fillField('#loginform-password', 'qwerty');
$I->click('Sign in');
$I->expectTo('see validations errors');
$I->see('You need to confirm your email address');

$I->amGoingTo('try to login with blocked account');
$user = $I->grabFixture('user', 'blocked');
$I->fillField('#loginform-login', $user->email);
$I->fillField('#loginform-password', 'qwerty');
$I->click('Sign in');
$I->expectTo('see blocking information');
$I->see('Your account has been blocked');

$I->amGoingTo('try to login with wrong credentials');
$user = $I->grabFixture('user', 'user');
$I->fillField('#loginform-login', $user->email);
$I->fillField('#loginform-password', 'wrong');
$I->click('Sign in');
$I->expectTo('see validations errors');
$I->see('Invalid login or password');

$I->amGoingTo('try to login with correct credentials');
$I->fillField('#loginform-login', $user->email);
$I->fillField('#loginform-password', 'qwerty');
$I->click('Sign in');
$I->dontSee('Login');
$I->see('Logout');
