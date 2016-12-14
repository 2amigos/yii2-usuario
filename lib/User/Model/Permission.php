<?php
namespace Da\User\Model;

use yii\rbac\Item;

class Permission extends AbstractAuthItem
{
    public function getType()
    {
        return Item::TYPE_PERMISSION;
    }
}
