<?php

/*
 * This file is part of the 2amigos/yii2-usuario project.
 *
 * (c) 2amigOS! <http://2amigos.us/>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Da\Usuario\Rbac\Contracts;

use yii\rbac\ManagerInterface;

interface AuthManagerInterface extends ManagerInterface
{
    /**
     * @param int|null $type
     * @param array    $excludeItems
     *
     * @return mixed
     */
    public function getItems(?int $type, array $excludeItems = []);

    /**
     * @param int $userId
     *
     * @return mixed
     */
    public function getItemsByUser(int $userId): array;

    /**
     * @param string $name
     *
     * @return mixed
     */
    public function getItem(string $name);
}
