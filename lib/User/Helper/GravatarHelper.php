<?php

namespace Da\User\Helper;

class GravatarHelper
{
    public function buildId($email)
    {
        return md5(strtolower(trim($email)));
    }

    public function getUrl($id, $size = 200)
    {
        return '//gravatar.com/avatar/'.$id.'?s='.$size;
    }
}
