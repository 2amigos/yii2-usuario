How to Implement Two Factor Auth (2FA)
======================================

Two Factor Authentication products add an additional layer of security. Typically, users are asked to prove their 
identity by providing simple credentials such as an email address and a password. A second factor (2F) adds an extra 
layer of unauthorized access protection by prompting the user to provide an additional means of authentication such as 
a physical token (e.g. a card) or an additional secret that only they know.

With this module is quite easy. It basically implements two factor authentication using the following 2amigos libraries: 

- [2amigos/2fa-library](https://github.com/2amigos/2fa-library)
- [2amigos/qrcode-library](https://github.com/2amigos/qrcode-library)

Enable Two Factor 
-----------------

Install required libraries with:
```
composer require 2amigos/2fa-library "^1.0"
composer require 2amigos/qrcode-library "^1.1"
```

Then enable two factor authentication in your config: 

```php 
'modules' => [
    'user' => [
        'class' => Da\User\Module::class,
        'enableTwoFactorAuthentication' => true
    ]
]
```

Now, when the user go to its settings via `user/settings`, it will display the option to enable two factor 
authentication or not. 

When enabled, the module will show a modal with a QrCode that has to be scanned by the Google Authenticator App 
(**Recommended**. You can download from 
[Google Play](https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2) or 
[iTunes](https://itunes.apple.com/us/app/google-authenticator/id388497605?mt=8)). 

The application will display a code that needs to be inserted into the modal input box. If code verification goes well, 
it will enable the two factor for the user. 

If a user has enabled the two factor, and after successfully login via username and email, it will render a new section 
where user will have to enter the code displayed on its Google Authenticator App in order to complete with the login 
process. 


### Recommended Reading

- [2amigos Two Factor Library Docs]()http://2fa-library.readthedocs.io/en/latest/)

© [2amigos](http://www.2amigos.us/) 2013-2019
