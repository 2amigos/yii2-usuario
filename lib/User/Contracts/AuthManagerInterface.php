<?php

namespace Da\User\Contracts;

use yii\rbac\ManagerInterface;

interface AuthManagerInterface extends ManagerInterface
{
    /**
     * @param int|null $type
     * @param array    $excludeItems
     *
     * @return mixed
     */
    public function getItems($type = null, $excludeItems = []);

    /**
     * @param int $userId
     *
     * @return mixed
     */
    public function getItemsByUser($userId);

    /**
     * @param string $name
     *
     * @return mixed
     */
    public function getItem($name);
}
