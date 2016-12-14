<?php
namespace Da\User\Model;

use yii\rbac\Item;


class Role extends AbstractAuthItem
{
    public function getType()
    {
        return Item::TYPE_ROLE;
    }
}
