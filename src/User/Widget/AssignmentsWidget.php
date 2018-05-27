<?php

/*
 * This file is part of the 2amigos/yii2-usuario project.
 *
 * (c) 2amigOS! <http://2amigos.us/>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Da\User\Widget;

use Da\User\Model\Assignment;
use Da\User\Service\UpdateAuthAssignmentsService;
use Da\User\Traits\AuthManagerAwareTrait;
use Da\User\Traits\ContainerAwareTrait;
use yii\base\InvalidConfigException;
use yii\base\InvalidParamException;
use yii\base\Widget;
use yii\helpers\ArrayHelper;

class AssignmentsWidget extends Widget
{
    use AuthManagerAwareTrait;
    use ContainerAwareTrait;

    /**
     * @var int ID of the user to whom auth items will be assigned
     */
    public $userId;
    /**
     * @var string[] the post parameters
     */
    public $params = [];

    /**
     * {@inheritdoc}
     *
     * @throws InvalidConfigException
     */
    public function init()
    {
        parent::init();
        if ($this->userId === null) {
            throw new InvalidConfigException(__CLASS__ . '::$userId is required');
        }
    }

    /**
     * {@inheritdoc}
     *
     * @throws InvalidParamException
     * @throws InvalidConfigException
     */
    public function run()
    {
        $model = $this->make(Assignment::class, [], ['user_id' => $this->userId]);

        if ($model->load($this->params)) {
            $this->make(UpdateAuthAssignmentsService::class, [$model])->run();
        }

        return $this->render(
            '/widgets/assignments/form',
            [
                'model' => $model,
                'availableItems' => $this->getAvailableItems(),
            ]
        );
    }

    /**
     * Returns all available auth items to be attached to the user.
     *
     * @return array
     */
    protected function getAvailableItems()
    {
        return ArrayHelper::map(
            $this->getAuthManager()->getItems(),
            'name',
            function ($item) {
                return empty($item->description)
                    ? $item->name
                    : $item->name . ' (' . $item->description . ')';
            }
        );
    }
}
