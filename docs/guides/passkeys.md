# How to Implement and Use Passkeys

<h3>Enabling Passkeys
-------------------------------
To enable passkey support in your application, follow these steps:

1. **Run the migration** that creates the `user_entity` table, which is required to store passkey credentials.  
   You can find the migration file here:  
   [`m000000_000011_create_user_entity_table.php`](../../src/User/Migration/m000000_000011_create_user_entity_table.php)

2. **Enable passkey functionality** in your application configuration by overriding the `user` module settings.  
   Add or update the following in your configuration file:

   ```php
   'modules' => [
       'user' => [
           'class' => Da\User\Module::class,
           'enablePasskeyLogin' => true,     // Enables login using passkeys
       ],
   ], 
      ```
after you set the `enablePasskeyLogin`, you'll be able to see the button `Passkey Login` in the login form of usuario. But it won't work if you don't have any passkey saved.

<h3>Views Paths
-------------------------------
If you have followed all the steps above now you're able to use passkeys!
So the paths to access the views are: 
- `/user/user-entity/index-passkey` the index page of the passkeys. In this page the user will be able to see all of his passkeys and manage them (update/create/delete).
- `user/user-entity/create-passkey` the page for creating a new passkey.

<h3>Extra Configurations
-------------------------------
You can add extra configurations, like adding an expiration date for the passkey or decide whether to show or not some notification related to passkeys to the user. To do so
add or update the following in your configuration file (like you did for enabling passkeys):

   ```php
   'modules' => [
       'user' => [
           'class' => Da\User\Module::class,
               'enablePasskeyPopUp' => true,    
               'enablePasskeyExpiringNotification' => true,    
               'maxPasskeysForUser' => 10,    
               'maxPasskeyAge' => 365,    
               'passkeyExpirationTimeLimit' => 30,    
       ],
   ], 
   ```         
Let's see in detail what these do:
- `enablePasskeyPopUp` Whether to enable a modal that suggest the user to use a passkey.
  This pop-up will be shown if the user doesn't have any passkeys, if the passkey login is enabled
  and only after the login.
- `enablePasskeyExpiringNotification` Whether to enable a modal that remembers the user that one (or more) of his
  passkeys are expiring. This message will be shown after the login. After the user dismiss the modal for 3 times they won't be notified anymore about that passkey that is expiring.
- `maxPasskeysForUser` The maximum number of passkey for user.
  Usally this value is set between 5 and 10 passkeys.
- `maxPasskeyAge` Time before the passkey will be eliminated since the last use.
    Usally this time is set between 6 and 12 months.
    This variable counts how many days before this will happen.
- `passkeyExpirationTimeLimit` The number of days before the user receives an alert saying that his passkey is expiring.
 Usually this value is set between 15 and 30 days.
         
Be aware that by default all the configurations are set to false. Only the 3 ones that are used to determine the age of the passkey are set by default with the values above. If you want to show the modals you need another extra step.

<h3>Passkey Widgets
-------------------------

<h4>- UserEntityPasskeyWidget</h4>

- **What it does**: Displays a pop-up immediately after a user logs in if they haven't registered any passkeys yet.
- **How to use it**: Just add this line in your view (e.g., layout or dashboard):

  ```php
  echo \\Da\\User\\Widget\\UserEntityPasskeyWidget::widget();
  ```

---

<h4>- UserEntityExpiringWidget</h4>

- **What it does**: Shows a notification once a user's passkey is approaching its expiration date.
- **How to use it**: Insert this in your view (e.g., layout or dashboard):

  ```php
  echo \\Da\\User\\Widget\\UserEntityExpiringWidget::widget();
  ```

---
