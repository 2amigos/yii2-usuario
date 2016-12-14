<?php
namespace Da\User\Search;

use yii\rbac\Item;

class PermissionSearch extends AbstractAuthItemSearch
{
    /**
     * @inheritdoc
     */
    public function getType()
    {
        return Item::TYPE_PERMISSION;
    }

}
