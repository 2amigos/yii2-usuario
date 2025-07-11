<?php

use yii\grid\GridView;
use yii\helpers\Html;

/** @var yii\data\ActiveDataProvider $dataProvider */
?>

<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'summary' => false,
    'tableOptions' => ['class' => 'table table-bordered table-hover'],
    'columns' => [
        ['class' => 'yii\grid\SerialColumn'],

        'name',
        'device_id',
        'sign_count',
        [
            'attribute' => 'last_used_at',
            'value' => function ($model) {
                return $model->last_used_at ?: '-';
            },
        ],
        'created_at',
        'expires_on',

        [
            'class' => 'yii\grid\ActionColumn',
            'header' => 'Actions',
            'headerOptions' => ['style' => 'width:120px; text-align:center;'],
            'contentOptions' => ['style' => 'text-align:center;'],
            'template' => '{update} {delete}',
            'buttons' => [
                'update' => function ($url, $model) {
                    return Html::a('Edit', ['user-entity/update-passkey', 'id' => $model->id], [
                        'class' => 'btn btn-sm btn-primary',
                        'title' => 'Edit Passkey',
                    ]);
                },
                'delete' => function ($url, $model) {
                    $url = ['user-entity/delete-passkey', 'id' => $model->id];
                    return Html::a('Delete', $url, [
                        'class' => 'btn btn-sm btn-danger',
                        'title' => 'Delete Passkey',
                        'style' => 'display:inline-block;',
                        'data' => [
                            'confirm' => 'Are you sure you want to delete this passkey?',
                            'method' => 'post',
                        ],
                    ]);
                },




            ],
        ],
    ],
]);
?>
