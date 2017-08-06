<?php

/**
 * @var Codeception\Scenario
 */

use tests\_fixtures\UserFixture;

$I = new FunctionalTester($scenario);
$I->wantTo('ensure that user update works');
$I->haveFixtures(['user' => UserFixture::className()]);

$user = $I->grabFixture('user', 'user');
$I->amLoggedInAs($user);

$I->amOnRoute('/user/admin/update', ['id' => $user->id]);

$I->fillField('#user-username', 'user');
$I->fillField('#user-email', 'updated_user@example.com');
$I->fillField('#user-password', 'newpassword');
$I->click('Update');
$I->see('Account details have been updated');

Yii::$app->user->logout();

$I->amOnRoute('/user/security/login');
$I->fillField('#loginform-login', 'updated_user@example.com');
$I->fillField('#loginform-password', 'newpassword');
$I->click('Sign in');
$I->see('Logout');
