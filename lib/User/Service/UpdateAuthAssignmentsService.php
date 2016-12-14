<?php

namespace Da\User\Service;

use Da\User\Contracts\ServiceInterface;
use Da\User\Model\Assignment;
use Da\User\Traits\AuthManagerTrait;

class UpdateAuthAssignmentsService implements ServiceInterface
{
    use AuthManagerTrait;

    protected $model;

    public function __construct(Assignment $model)
    {
        $this->model = $model;
    }

    public function run()
    {
        if ($this->model->validate()) {
            return false;
        }

        if (!is_array($this->model->items)) {
            $this->model->items = [];
        }

        $assignedItems = $this->getAuthManager()->getItemsByUser($this->model->user_id);
        $assignedItemsNames = array_keys($assignedItems);

        foreach (array_diff($assignedItemsNames, $this->model->items) as $item) {
            $this->model->getAuthManager()->revoke($assignedItems[$item], $this->model->user_id);
        }

        foreach (array_diff($this->model->items, $assignedItemsNames) as $item) {
            $this->getAuthManager()->assign($this->getAuthManager()->getItem($item), $this->model->user_id);
        }

        return $this->model->updated = true;
    }
}
