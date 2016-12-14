<?php

namespace Da\User\Search;

use yii\rbac\Item;

class RoleSearch extends AbstractAuthItemSearch
{
    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return Item::TYPE_ROLE;
    }
}
