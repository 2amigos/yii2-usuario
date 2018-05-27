<?php

/*
 * This file is part of the 2amigos/yii2-usuario project.
 *
 * (c) 2amigOS! <http://2amigos.us/>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Da\User\Component;

use Da\User\Contracts\AuthManagerInterface;
use yii\db\Query;
use yii\rbac\DbManager;

class AuthDbManagerComponent extends DbManager implements AuthManagerInterface
{
    /**
     * @param int|null $type         If null will return all auth items
     * @param array    $excludeItems Items that should be excluded from result array
     *
     * @return array
     */
    public function getItems($type = null, $excludeItems = [])
    {
        $query = (new Query())->from($this->itemTable);

        if ($type !== null) {
            $query->where(['type' => $type]);
        } else {
            $query->orderBy('type');
        }

        foreach ($excludeItems as $name) {
            $query->andWhere('name <> :item', ['item' => $name]);
        }

        $items = [];

        foreach ($query->all($this->db) as $row) {
            $items[$row['name']] = $this->populateItem($row);
        }

        return $items;
    }

    /**
     * Returns both roles and permissions assigned to user.
     *
     * @param int $userId
     *
     * @return array
     */
    public function getItemsByUser($userId)
    {
        if (empty($userId)) {
            return [];
        }

        $query = (new Query())
            ->select('b.*')
            ->from(['a' => $this->assignmentTable, 'b' => $this->itemTable])
            ->where('{{a}}.[[item_name]]={{b}}.[[name]]')
            ->andWhere(['a.user_id' => (string)$userId]);

        $roles = [];
        foreach ($query->all($this->db) as $row) {
            $roles[$row['name']] = $this->populateItem($row);
        }

        return $roles;
    }

    /**
     * {@inheritdoc}
     */
    public function getItem($name)
    {
        return parent::getItem($name);
    }
}
