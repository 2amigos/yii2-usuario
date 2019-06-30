<?php

/*
 * This file is part of the 2amigos/yii2-usuario project.
 *
 * (c) 2amigOS! <http://2amigos.us/>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Da\Usuario\Base\Helper;

class Gravatar
{
    public function buildId(string $email): string
    {
        return md5(strtolower(trim($email)));
    }

    public function getUrl(int $id, int $size = 200): string
    {
        return '//gravatar.com/avatar/' . $id . '?s=' . $size;
    }
}
