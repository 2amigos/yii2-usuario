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
use yii\base\InvalidArgumentException;
use yii\db\Expression;
use yii\db\Query;
use yii\rbac\DbManager;
use yii\rbac\Role;

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


    /**
     * @inheritdoc
     * @param bool|integer $recursive If the roles are to be calculated recursively. If an integer is passed it will limit the depth of the
     *  recursion to the given number (e.g. 1 would also get ids from users assigned to only the parents of the given role).
     * @override to add possibility to get the ids of users assigned to roles that are parents of the given one.
     * @since 1.6.1
     */
    public function getUserIdsByRole($roleName, $recursive = false)
    {
        if(!$recursive || empty($roleName)) {
            return parent::getUserIdsByRole($roleName);
        }

        $roles = $this->getParentRoles($roleName, $recursive === true ? null : $recursive);
        $userIds = array_reduce($roles, function ($ids, $role) {
            $roleIds = parent::getUserIdsByRole($role->name);
            return array_merge($ids, $roleIds);
        }, []);
        return array_unique($userIds);
    }


    /**
     * Returns parent roles of the role specified. Depth isn't limited.
     * @param string $roleName name of the role to file parent roles for
     * @param null|integer $depth The depth to which to search for parents recursively, if null it won't have any limit. Defaults to `null`.
     * @return Role[] Child roles. The array is indexed by the role names.
     * First element is an instance of the parent Role itself.
     * @throws \yii\base\InvalidParamException if Role was not found that are getting by $roleName
     * @since 1.6.1
     */
    public function getParentRoles($roleName, $depth = null)
    {
        $role = $this->getRole($roleName);

        if ($role === null) {
            throw new InvalidArgumentException("Role \"$roleName\" not found.");
        }

        $result = [];
        $this->getParentsRecursive($roleName, $result, $depth);

        $roles = [$roleName => $role];

        $roles += $result;

        return $roles;
    }

    /**
     * Recursively finds all parents and grandparents of the specified item.
     * @param string $name the name of the item whose children are to be looked for.
     * @param array $result the children and grand children (in array keys)
     * @param null|integer $depth The depth to which to search recursively, if null it won't have any limit. Defaults to `null`.
     * @since 1.6.1
     */
    protected function getParentsRecursive($name, &$result = [], &$depth = null)
    {
        $depth -= 1; // Cannot use -- because we have to cast `null` to integer
        $query = (new Query())
            ->select(['name', 'type', 'description', 'rule_name', 'data', 'created_at', 'updated_at'])
            ->from([$this->itemTable, $this->itemChildTable])
            ->where(['child' => $name, 'name' => new Expression('[[parent]]')]);

        foreach ($query->all($this->db) as $row) {
            if(isset($result[$row['name']])) {
                continue;
            }
            $result[$row['name']] = $this->populateItem($row);
            // If we have yet to reach the maximum depth, we continue.
            // If $depth was orginally `null` it'd start from -1 so decrements will never make reach 0
            if($depth !== 0) {
                $this->getParentsRecursive($row['name'], $result);
            }
        }
    }
}
