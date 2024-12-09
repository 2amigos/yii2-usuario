<?php

/**
 * @var Codeception\Scenario
 */

use tests\_fixtures\ProfileFixture;
use tests\_fixtures\UserFixture;


$I = new FunctionalTester($scenario);
$I->haveFixtures([
    'user' => UserFixture::class,
    'profile' => ProfileFixture::class
]);
$user = $I->grabFixture('user', 'user');
$secondUser = $I->grabFixture('user', 'seconduser');
$adminUser = $I->grabFixture('user', 'admin');
$I->wantTo('Ensure that profile profile pages are shown only to when user has correct permissions and else forbidden');

Yii::$app->getModule('user')->profileVisibility = \Da\User\Controller\ProfileController::PROFILE_VISIBILITY_OWNER;
Yii::$app->getModule('user')->administrators = ['admin'];

$I->amLoggedInAs($user);
$I->amGoingTo('try to open users own profile page');
$I->amOnRoute('/user/profile/show', ['id' => $user->id]);
$I->expectTo('See the profile page');
$I->dontSee('Forbidden');
$I->see('Joined on');

$I->amGoingTo('Profile visibility::OWNER: try to open another users profile page');
$I->amOnRoute('/user/profile/show', ['id' => $secondUser->id]);
$I->expectTo('See the profile page');
$I->see('Forbidden');
$I->dontSee('Joined on');

Yii::$app->user->logout();
$I->amGoingTo('Profile visibility::OWNER: try to open users profile page as guest');
$I->amOnRoute('/user/profile/show', ['id' => $user->id]);
$I->expectTo('See the profile page');
$I->see('Forbidden');
$I->dontSee('Joined on');


Yii::$app->getModule('user')->profileVisibility = \Da\User\Controller\ProfileController::PROFILE_VISIBILITY_ADMIN;
$I->amLoggedInAs($user);
$I->amGoingTo('Profile visibility::PROFILE_VISIBILITY_ADMIN: try to open users own profile page');
$I->amOnRoute('/user/profile/show', ['id' => $user->id]);
$I->expectTo('See the profile page');
$I->dontSee('Forbidden');
$I->see('Joined on');

$I->amGoingTo('Profile visibility::PROFILE_VISIBILITY_ADMIN: try to open another users profile page as regular user');
$I->amOnRoute('/user/profile/show', ['id' => $secondUser->id]);
$I->expectTo('See the profile page');
$I->see('Forbidden');
$I->dontSee('Joined on');

$I->amLoggedInAs($adminUser);
$I->amGoingTo('Profile visibility::PROFILE_VISIBILITY_ADMIN: try to open another users profile page as admin');
$I->amOnRoute('/user/profile/show', ['id' => $user->id]);
$I->expectTo('See the profile page');
$I->dontSee('Forbidden');
$I->see('Joined on');

Yii::$app->user->logout();
$I->amGoingTo('Profile visibility::PROFILE_VISIBILITY_ADMIN: try to open users profile page as guest');
$I->amOnRoute('/user/profile/show', ['id' => $user->id]);
$I->expectTo('See the profile page');
$I->see('Forbidden');
$I->dontSee('Joined on');


Yii::$app->getModule('user')->profileVisibility = \Da\User\Controller\ProfileController::PROFILE_VISIBILITY_USERS;
$I->amLoggedInAs($user);
$I->amGoingTo('Profile visibility::PROFILE_VISIBILITY_USERS: try to open users own profile page');
$I->amOnRoute('/user/profile/show', ['id' => $user->id]);
$I->expectTo('See the profile page');
$I->dontSee('Forbidden');
$I->see('Joined on');

$I->amGoingTo('Profile visibility::PROFILE_VISIBILITY_USERS: try to open another users profile page as regular user');
$I->amOnRoute('/user/profile/show', ['id' => $secondUser->id]);
$I->expectTo('See the profile page');
$I->dontSee('Forbidden');
$I->see('Joined on');

$I->amLoggedInAs($adminUser);
$I->amGoingTo('Profile visibility::PROFILE_VISIBILITY_USERS: try to open another users profile page as admin');
$I->amOnRoute('/user/profile/show', ['id' => $user->id]);
$I->expectTo('See the profile page');
$I->dontSee('Forbidden');
$I->see('Joined on');

Yii::$app->user->logout();
$I->amGoingTo('Profile visibility::PROFILE_VISIBILITY_USERS: try to open users profile page as guest');
$I->amOnRoute('/user/profile/show', ['id' => $user->id]);
$I->expectTo('See the profile page');
$I->see('Forbidden');
$I->dontSee('Joined on');

Yii::$app->getModule('user')->profileVisibility = \Da\User\Controller\ProfileController::PROFILE_VISIBILITY_PUBLIC;

Yii::$app->user->logout();
$I->amGoingTo('Profile visibility::PROFILE_VISIBILITY_PUBLIC: try to open users profile page as guest');
$I->amOnRoute('/user/profile/show', ['id' => $user->id]);
$I->expectTo('See the profile page');
$I->dontSee('Forbidden');
$I->see('Joined on');

