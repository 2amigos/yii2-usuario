# CHANGELOG

## 1.1.2 - Work in progress
- Enh #137: Added the ability to make `enableAutologin` configurable (pappfer)
- Enh #135: Added Estonian translation (tonisormisson)
- Bug #133: Fix user search returning no results in admin page (phiurs)
- Bug #125: Fix validation in non-ajax requests (faenir)
- Bug #122: Fix wrong email message for email address change (liviuk2)

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
