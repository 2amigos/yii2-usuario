<?php

use yii\grid\GridView;
use yii\helpers\Html;

/** @var yii\data\ActiveDataProvider $dataProvider */


$this->registerCss(<<<CSS
.btn-no-style {
    background: none;
    border: none;
    padding: 0;
    margin: 0 5px;
    color: inherit;
    cursor: pointer;
    box-shadow: none;
    text-decoration: none;
}
CSS);

//needed for loading icons without altering the style of the page
$this->registerCss(<<<CSS
@font-face {
  font-family: 'Glyphicons Halflings';
  src: url('https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/fonts/glyphicons-halflings-regular.woff2') format('woff2'),
       url('https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/fonts/glyphicons-halflings-regular.woff') format('woff');
}

.glyphicon {
  position: relative;
  top: 1px;
  display: inline-block;
  font-family: 'Glyphicons Halflings';
  font-style: normal;
  font-weight: normal;
  line-height: 1;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
}

.glyphicon-pencil:before { content: "\\270f"; }
.glyphicon-trash:before { content: "\\e020"; }
.glyphicon-user:before { content: "\\e008"; }
.glyphicon-time:before { content: "\\e023"; }
.glyphicon-flash:before { content: "\\e162"; }
CSS);
?>

<div class="table-responsive">
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
                'value' => fn($model) => $model->last_used_at ? Yii::$app->formatter->asDatetime($model->last_used_at) : '-',
            ],
            [
                'attribute' => 'created_at',
                'value' => fn($model) => Yii::$app->formatter->asDatetime($model->created_at),
            ],
            [
                'label' => Yii::t('usuario','Expiration date'),
                'value' => function ($model) {
                    $module = Yii::$app->getModule('user');
                    $maxAgeDays = $module->maxPasskeyAge;
                    $lastUsed = new \DateTime($model->last_used_at ?? $model->created_at);
                    $lastUsed->modify("+{$maxAgeDays} days")->format("Y-m-d");
                    $lastUsed = Yii::$app->formatter->asDate($lastUsed);
                    return $lastUsed;

                }
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'header' => Yii::t('usuario', 'Actions'),
                'headerOptions' => ['style' => 'width:120px; text-align:center;'],
                'contentOptions' => ['style' => 'text-align:center;'],
                'template' => '{update} {delete}',
                'buttons' => [
                    'update' => fn($url, $model) =>
                    Html::a(
                        '<span class="glyphicon glyphicon-pencil"></span>',
                        ['user-entity/update-passkey', 'id' => $model->id],
                        [
                            'class' => 'btn-no-style',
                            'title' => Yii::t('usuario', 'Edit Passkey'),
                        ]
                    ),
                    'delete' => fn($url, $model) =>
                    Html::a(
                        '<span class="glyphicon glyphicon-trash"></span>',
                        ['user-entity/delete-passkey', 'id' => $model->id],
                        [
                            'class' => 'btn-no-style',
                            'title' => Yii::t('usuario', 'Delete Passkey'),
                            'data' => [
                                'confirm' => Yii::t('usuario', 'Are you sure you want to delete this passkey?'),
                                'method' => 'post',
                            ],
                        ]
                    ),
                ],

            ],
        ],
    ]); ?>
</div>
