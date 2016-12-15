<?php

namespace tests\_fixtures;

use yii\test\ActiveFixture;

class ProfileFixture extends ActiveFixture
{
    public $modelClass = 'Da\User\Model\Profile';

    public $depends = [
        'tests\_fixtures\UserFixture',
    ];
}
