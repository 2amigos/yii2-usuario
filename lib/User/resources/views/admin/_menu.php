<?php

use yii\bootstrap\Nav;

?>

<?= Nav::widget(
    [
        'options' => [
            'class' => 'nav-tabs',
            'style' => 'margin-bottom: 15px',
        ],
        'items' => [
            [
                'label' => Yii::t('user', 'Users'),
                'url' => ['/user/admin/index'],
            ],
            [
                'label' => Yii::t('user', 'Roles'),
                'url' => ['/rbac/role/index']
            ],
            [
                'label' => Yii::t('user', 'Permissions'),
                'url' => ['/rbac/permission/index']
            ],
            [
                'label' => Yii::t('user', 'Create'),
                'items' => [
                    [
                        'label' => Yii::t('user', 'New user'),
                        'url' => ['/user/admin/create'],
                    ],
                    [
                        'label' => Yii::t('user', 'New role'),
                        'url' => ['/rbac/role/create']
                    ],
                    [
                        'label' => Yii::t('user', 'New permission'),
                        'url' => ['/rbac/permission/create']
                    ],
                ],
            ],
        ],
    ]
) ?>
