<?php

namespace Da\User\Event;

use Da\User\Model\Profile;
use yii\base\Event;

class ProfileEvent extends Event
{
    protected $profile;

    public function __construct(Profile $profile, array $config = [])
    {
        $this->profile = $profile;

        return parent::__construct($config);
    }

    public function getProfile()
    {
        return $this->profile;
    }
}
