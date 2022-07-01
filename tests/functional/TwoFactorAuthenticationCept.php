<?php

/**
 * @var Codeception\Scenario
 */

use tests\_fixtures\UserFixture;
use tests\_fixtures\PermissionFixture;
use tests\_fixtures\AssignmentFixture;
use tests\_fixtures\ProfileFixture;


$I = new FunctionalTester($scenario);
$I->wantTo('ensure that two factor authentication check works');
$I->haveFixtures(['user' => UserFixture::className()]);
$I->haveFixtures(['permission' => PermissionFixture::className()]);
$I->haveFixtures(['assignment' => AssignmentFixture::className()]);

$I->amGoingTo('try to login with user having two factor authentication enabled');
Yii::$app->getModule('user')->enableTwoFactorAuthentication = true;
$I->amOnRoute('/user/security/login');
$user = $I->grabFixture('user', 'user_with_2fa_enabled');
$I->fillField('#loginform-login', $user->email);
$I->fillField('#loginform-password', 'qwerty');
$I->click('Sign in');
$I->expectTo('See form to insert two factor authentication code');
$I->see('Two factor authentication code');

$I->amGoingTo('try to login with user permission admin, having two factor authentication disabled');
Yii::$app->getModule('user')->enableTwoFactorAuthentication = true;
Yii::$app->getModule('user')->twoFactorAuthenticationForcedPermissions = ['admin'];
$I->haveFixtures(['user' => UserFixture::className(), 'profile' => ProfileFixture::className()]);
$I->amOnRoute('/user/security/login');
$user = $I->grabFixture('user', 'user');
$I->fillField('#loginform-login', $user->email);
$I->fillField('#loginform-password', 'qwerty');
$I->click('Sign in');
$I->expectTo('The user must be forced to enable two factor authentication');
$I->see('Every user having your role has two factor authentication mandatory, you must enable it');
Yii::$app->user->logout();

$I->amGoingTo('try to login with correct credentials when two factor authentication is disabled on the module');
Yii::$app->getModule('user')->enableTwoFactorAuthentication = false;
$I->amOnRoute('/user/security/login');
$I->amGoingTo('try to login with correct credentials');
$user = $I->grabFixture('user', 'user');
$I->fillField('#loginform-login', $user->email);
$I->fillField('#loginform-password', 'qwerty');
$I->click('Sign in');
$I->dontSee('Login');
$I->see('Logout');



