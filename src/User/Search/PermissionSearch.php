<?php

/*
 * This file is part of the 2amigos/yii2-usuario project.
 *
 * (c) 2amigOS! <http://2amigos.us/>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Da\User\Search;

use yii\rbac\Item;

class PermissionSearch extends AbstractAuthItemSearch
{
    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return Item::TYPE_PERMISSION;
    }
}
