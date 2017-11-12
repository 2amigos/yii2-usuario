<?php

/*
 * This file is part of the 2amigos/yii2-usuario project.
 *
 * (c) 2amigOS! <http://2amigos.us/>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Da\User\Service;

use Da\User\Contracts\ServiceInterface;
use Da\User\Model\Rule;
use Da\User\Traits\AuthManagerAwareTrait;
use Da\User\Traits\ContainerAwareTrait;
use Exception;

class AuthRuleEditionService implements ServiceInterface
{
    use AuthManagerAwareTrait;
    use ContainerAwareTrait;

    protected $model;

    public function __construct(Rule $model)
    {
        $this->model = $model;
    }

    public function run()
    {
        if (!$this->model->validate() || (!in_array($this->model->scenario, ['create', 'update'], false))) {
            return false;
        }

        $rule = $this->make($this->model->className, [], ['name' => $this->model->name]);

        try {
            if ($this->model->scenario === 'create') {
                $this->getAuthManager()->add($rule);
            } else {
                $this->getAuthManager()->update($this->model->previousName, $rule);
            }
            $this->getAuthManager()->invalidateCache();
        } catch (Exception $e) {
            return false;
        }

        return true;
    }
}
