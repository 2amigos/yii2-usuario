# CHANGELOG

## work in progress

## 1.5.1 April 5, 2020
 - Fix #370: Extending view fix (effsoft)
 - Fix #306: Add event for failed login (ivan-cc)
 - Fix #347: Only pass fields known to User model in registrationControl->actionRegister() (BillHeaton)
 - Fix #346: Update ReCaptcha guide to not use AJAX  (BillHeaton)
 - Fix #345: Update ReCaptcha guide to add scenarios() in recoveryForm  (BillHeaton)
 - Fix #307: Fix French translation (arollmann)
 - Fix #316: Fix new response from Google OAuth Api (Julian-B90)
 - Fix #321: Fix new response from LinkedIn OAuth Api (tonydspaniard) 
 - Fix #322: Fix boolean values in migrations for SQL server (tsdogs)
 - Enh #325: Added support for sqlite3 (santilin)
 - Fix #326: Fix rule for the user auth_tf_enabled field (santilin)
 - Fix #290: Fix wrong email message for resending confirmation (tonydspaniard)
 - Enh #269: Added help documentation to console commands (tonydspaniard)
 - Fix #244: Fix forced inclusion of a suggested class (tonydspaniard)
 - Fix user event triggering in admin controller (maxxer)
 - Enh #331: Added Ukrainian translations (kwazaro)
 - Enh #324: Added option to restrict user assignments to roles only (CheckeredFlag)
 - Enh #224: Added option to require consent (eseperio)
 - Enh: Added classMap for MailService (necrox87)

## 1.5.0 April 19, 2019
 - Fix: Fix condition in EmailChangeService (it was always false) (borisaeric)
 - Fix #198: Updated translations by quique, bizley, TonisOrmisson, guogan, Dezinger, maxxer, wautvda, mrbig00, fabiomlferreira, WeeSee
 - Fix #209: Doc fix. allowAccountDelete default value is false (Dezinger)
 - Fix #211: Migration boolean default value set to FALSE instead 0 (Dezinger)
 - Fix #213: Migration sql syntax fix (Dezinger)
 - Ehn #131: 2FA libraries now optional (maxxer)
 - Ehn #187: Add GDPR features (Eseperio)
 - Enh #184: Add `last-login-ip` capture capability (kartik-v)
 - Enh: Changed `View::render()` calls in views to use absolute paths (ajmedway)
 - Fix #169: Fix bug in ReCaptchaComponent (BuTaMuH)
 - Fix #168: Fix spelling in russian language (EvgenyOrekhov)
 - Fix #195: UserCreateService: check if we're from web before setting flash message (maxxer)
 - Enh: Improvements to the admin responsive design (wautvda)
 - Enh: Add controller module class reference (TonisOrmisson)
 - Enh: Replace the deprecated InvalidParamException in ClassMapHelper (TonisOrmisson)
 - Fix #242: Add POST filter for `admin/force-password-change` action (bscheshirwork)
 - Enh #251: Use `asset-packagist` instead of `fxp-asset` if you run it as a module without having a project around (bscheshirwork)
 - Fix #252: Delete check for unexpected property `allowPasswordRecovery` for resend email by admin (bscheshirwork)
 - Fix #254: Rename `GDPR` properties to `lowerCamelCase` style (bscheshirwork)
 - Enh #253: Add PHPDoc for events class (bscheshirwork)
 - Fix #258: Rename `GDPR` delete action to `lowerCamelCase`/`dash` style (bscheshirwork)
 - Fix #271: Add closure support for `from` email address; Change default sender to `supportEmail` (bscheshirwork)
 - Fix #276: Fix missing translatable strings
 - Enh #249: Show message `email send if possible` any time on reset password request (bscheshirwork)
 - Enh #282: Allows customization of controller namespace (maxxer)
 - Enh #303: Added French translation (pde159)
 - Fix #304: Fixed broken regex character class (CheckeredFlag)

## 1.1.4 - February 19, 2018
- Enh: Check enableEmailConfirmation on registration (faenir)
- Fix #154: Fix DateTime constructor with Unix timestamps (tonydspaniard)

## 1.1.2-3 - February 9, 2018
- Bug: Bugfix for Model events UserEvent::EVENT_BEFORE_CONFIRMATION and UserEvent::EVENT_AFTER_CONFIRMATION (ajmedway)
- Bug: Bugfix for Model events UserEvent::EVENT_BEFORE_CREATE and UserEvent::EVENT_AFTER_CREATE (ajmedway)
- Enh #137: Added the ability to make `enableAutologin` configurable (pappfer)
- Enh #135: Added Estonian translation (tonisormisson)
- Bug #133: Fix user search returning no results in admin page (phiurs)
- Bug #125: Fix validation in non-ajax requests (faenir)
- Bug #122: Fix wrong email message for email address change (liviuk2)
- Bug #102: Implemented password expiration feature (maxxer)
- Enh #143: Introduced "conflict" configuration in composer.json (maxxer)
- Enh #145: Allowed the `+` sign in username (maxxer)
- Bug #9:   Documentation about migration from Dektrium tools (maxxer)
- Bug #110: Honor `enableFlashMessages` in `PasswordRecoveryService` (maxxer)

## 1.1.1 - November 27, 2017
- Bug #115: Convert client_id to string because pgsql fail with type convertion (Dezinger)
- Bug #119: Security fix: add AccessControl to RuleController (Dezinger)
- Enh #120: 2FA i18n russian translation (Dezinger)
- Bug #111: Fix migration for PostgreSQL DBMS (MKiselev)
- Bug #106: Correct exception value returned in `MailEvent::getException` (kartik-v)
- Enh #99:  Added German translation (jkmssoft)
- Enh #100: Added pt-BR translation (gugoan)
- Enh #105: Consolidate 2fa messages (maxxer)
- Fix #108: Use main logger app (tonydspaniard)
- Enh #109: Make use of better classes names (tonydspaniard)

## 1.1.0 - October 22, 2017
- Enh #91: Documentation for Mail events (kartik-v)
- Enh #79: Enhancements to Mailer exception handling and events (kartik-v)
- Fix #85: External links should open in a new tab|window (eseperio)
- Enh #23: Provide administrator with an option to reset user password (tonydspaniard)
- Enh #55: Provide google recaptcha mechanism (tonydspaniard)
- Fix #20: Allow the assignment of a role on user creation via console (tonydspaniard)
- Fix #59: Add instructions to add rbac migration path (tonydspaniard)
- Fix #68: Fix user events documentation and events raised from User model (tonydspaniard)
- Fix #69: Log level when user can't register should be L_ERROR (tonydspaniard)
- Enh #81: Update `AccessRuleFilter` to evaluate `roleParams` (kartik-v)
- Enh #56: Added two factor authentication (tonydspaniard)
- Fix #63: Fix selectize version (tonydspaniard)
- Enh #65: Updated Romanian translation (mrbig00)
- Enh #61: Updated Russian translation (faenir)
- Enh #70: Allow permission-permission parent-child relationship (Philosoft)
- Enh #82: Updated Polish translation (bizley)
- Enh #83: Updated Russian translation (Katenkka)
- Fix #87: Fix wrong documentation info (tonydspaniard)
- Fix #86: Fix view location bug (tonydspaniard)

## 1.0.13 - August 12, 2017
- Fix #49: Fix wrong call of method make() for set attributes (MKiselev)
- Enh #46: Use safeUp()/safeDown() instead up()/down() in migrations (MKiselev)
- Fix #51: Typo fix rememberLoginLifeSpan to rememberLoginLifespan (MKiselev)
- Fix #58: Last login fix (pappfer)

## 1.0.12 - August 6, 2017
- Bug Fix: Modify ResetPasswordService to forcely update password_hash field (tonydspaniard) 
- Bug Fix: Fixed wrong routing misspell (tonydspaniard) 
- Enh #41: Remove deprecated package yii2-codeception (tonydspaniard)
- Enh #45: Added option to display the password to the welcome email (tonydspaniard)
- Fix #44: Check if the password is empty instead for null value (tonydspaniard)
- Fix #43: Added Table options according to driver type (tonydspaniard)
- Fix #42: Allow setting permissions as children to roles (kurounin)

## 1.0.10-11 - July 25, 2017
- Fix #37: Fix bower alias in test environment (tekord)
- Enh #32: Added Italian Translation (maxxer)
- Fix #30: Prefill username and email in SettingsForm (mattheobjornson)
- Enh #39: Added `last_login_at` field to user table (pappfer)

## 1.0.9 - July 19, 2017
- Enh #22: Added impersonation feature (tonydspaniard)

## 1.0.8 - July 16, 2017 

- Enh #25: Added option to manage rules (tonydspaniard)
- Enh #25: Added SelectizeDropDownList widget to Role and Permission forms (tonydspaniard)
