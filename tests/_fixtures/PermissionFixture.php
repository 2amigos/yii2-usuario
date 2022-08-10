<?php

namespace tests\_fixtures;

use yii\test\ActiveFixture;

class PermissionFixture extends ActiveFixture
{
    public $modelClass = 'Da\User\Model\Permission';
    public $tableName = 'auth_item';
}
