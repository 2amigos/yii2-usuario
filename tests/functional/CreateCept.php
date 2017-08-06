<?php

/**
 * @var Codeception\Scenario
 */

use tests\_fixtures\UserFixture;

$I = new FunctionalTester($scenario);
$I->wantTo('ensure that user creation works');
$I->haveFixtures(['user' => UserFixture::className()]);

$user = $I->grabFixture('user', 'user');

$I->amLoggedInAs($user);

$I->amOnRoute('/user/admin/create');

$I->amGoingTo('try to create user with empty fields');
$I->fillField('#user-username', '');
$I->fillField('#user-email', '');
$I->fillField('#user-password', '');
$I->click('Save');

$I->expectTo('see validations errors');
$I->see('Username cannot be blank.');
$I->see('Email cannot be blank.');

$I->fillField('#user-username', 'foobar');
$I->fillField('#user-email', 'foobar@example.com');
$I->fillField('#user-password', 'foobar');
$I->click('Save');
$I->see('User has been created');

Yii::$app->user->logout();
$I->amOnRoute('/user/security/login');
$I->fillField('#loginform-login', 'foobar@example.com');
$I->fillField('#loginform-password', 'foobar');
$I->click('Sign in');
$I->see('Logout');
