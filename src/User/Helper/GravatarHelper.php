<?php

/*
 * This file is part of the 2amigos/yii2-usuario project.
 *
 * (c) 2amigOS! <http://2amigos.us/>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Da\User\Helper;

class GravatarHelper
{
    public function buildId($email)
    {
        return md5(strtolower(trim($email)));
    }

    public function getUrl($id, $size = 200)
    {
        return '//gravatar.com/avatar/' . $id . '?s=' . $size;
    }
}
