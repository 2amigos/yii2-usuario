# Migrating from v1 to v2


The main change in v2 is decoupling the UI framework from the module base code in order to allow supporting mulitple 
UI frameworks simultaneously. Eg bootstrab 3/4/5 or any other ui framework. To be able to do this v2 is not including 
the Bootstrap as a required dependency anymore. 

The module includes the view files for both Bootstrap 3 and Bootstrap 5. The default views for v2 are Bootstrap 5, but
either way the bootstrap dependencies must be specified by your project composer.json requirements depending on which 
version you choose to use. 

## Using with old Bootstrap 3
In order to continue with the _old_ Bootstrap 3 views. you need to:

1. make sure you have the followin packakges included in your composer.json "require" section:
```
"2amigos/yii2-selectize-widget": "^1.1",
"yiisoft/yii2-bootstrap": "^2.0",
```

2. Change the usuario Module 'viewPath' paramater to a folder containing Bootstrap 3 views, eg the included:
`'viewPath' => '@Da/User/resources/views/bootstrap3',`

## Using with old Bootstrap 5
1. make sure you have the followin packakges included in your composer.json "require" section:
```
"yiisoft/yii2-bootstrap5",
"kartik-v/yii2-widget-select",
"twbs/bootstrap-icons",
```
2. The default 'viewPath' for v2 Module is Bootstrap 5 views in '@Da/User/resources/views/bootstrap5'. 
