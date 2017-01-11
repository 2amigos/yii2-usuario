Other for Developers
====================

Tests
-----

1. First of all
2. Running tests: `./vendor`

Code Style Checkers and Mess Detectors
--------------------------------------

## [squizlabs/PHP_CodeSniffer](https://github.com/squizlabs/PHP_CodeSniffer)

Global settings file: `phpcs.xml.dist` (added into Git)  
Local settings file (you can add it locally): `phpcs.xml` (ignored by Git)

Run checking by `PHP_CodeSniffer`: `./vendor/bin/phpcs`  
Show check report in patch form: `./vendor/bin/phpcs --report=diff`  
Automatically fix all fixable issues: `./vendor/bin/phpcbf`

## [FriendsOfPHP/PHP-CS-Fixer](https://github.com/FriendsOfPHP/PHP-CS-Fixer)

Global settings file: `.php_cs.dist` (added into Git)  
Local settings file (you can add it locally): `.php_cs` (ignored by Git)

Run checking by `PHP-CS-Fixer`: `./vendor/bin/php-cs-fixer fix --dry-run`  
Automatically fix all fixable issues: `./vendor/bin/php-cs-fixer fix`

## [phpmd/phpmd](https://github.com/phpmd/phpmd)

TODO:
