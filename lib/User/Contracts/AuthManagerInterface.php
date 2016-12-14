<?php
namespace Da\User\Contracts;

use yii\rbac\ManagerInterface;

interface AuthManagerInterface extends ManagerInterface
{
    /**
     * @param  integer|null $type
     * @param  array $excludeItems
     *
     * @return mixed
     */
    public function getItems($type = null, $excludeItems = []);

    /**
     * @param  integer $userId
     *
     * @return mixed
     */
    public function getItemsByUser($userId);

    /**
     * @param  string $name
     *
     * @return mixed
     */
    public function getItem($name);
}
