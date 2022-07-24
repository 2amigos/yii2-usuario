<?php

/*
 * This file is part of the 2amigos/yii2-usuario project.
 *
 * (c) 2amigOS! <http://2amigos.us/>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

use yii\bootstrap4\Nav;

?>

<?= Nav::widget(
    [
        'options' => [
            'class' => 'nav-tabs',
            'style' => 'margin-bottom: 15px',
        ],
        'items' => [
            [
                'label' => Yii::t('usuario', 'Users'),
                'url' => ['/user/admin/index'],
            ],
            [
                'label' => Yii::t('usuario', 'Roles'),
                'url' => ['/user/role/index'],
            ],
            [
                'label' => Yii::t('usuario', 'Permissions'),
                'url' => ['/user/permission/index'],
            ],
            [
                'label' => Yii::t('usuario', 'Rules'),
                'url' => ['/user/rule/index'],
            ],
            [
                'label' => Yii::t('usuario', 'Create'),
                'items' => [
                    [
                        'label' => Yii::t('usuario', 'New user'),
                        'url' => ['/user/admin/create'],
                    ],
                    [
                        'label' => Yii::t('usuario', 'New role'),
                        'url' => ['/user/role/create'],
                    ],
                    [
                        'label' => Yii::t('usuario', 'New permission'),
                        'url' => ['/user/permission/create'],
                    ],
                    [
                        'label' => Yii::t('usuario', 'New rule'),
                        'url' => ['/user/rule/create'],
                    ],
                ],
            ],
        ],
    ]
) ?>
