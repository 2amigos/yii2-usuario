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
use Yii;
use yii\base\InvalidConfigException;
use yii\base\InvalidParamException;
use yii\base\Widget;
use yii\helpers\ArrayHelper;
use yii\rbac\Item;

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

        $items[Yii::t('usuario', 'Roles')] = $this->getAvailableItems(Item::TYPE_ROLE);
        if (!Yii::$app->getModule('user')->restrictUserPermissionAssignment) {
            $items[Yii::t('usuario', 'Permissions')] = $this->getAvailableItems(Item::TYPE_PERMISSION);
        }

        return $this->render(
            '/widgets/assignments/form',
            [
                'model' => $model,
                'availableItems' => $items,
            ]
        );
    }

    /**
     * Returns available auth items to be attached to the user.
     *
     * @param int|null type of auth items or null to return all
     * @param null|mixed $type
     *
     * @return array
     */
    protected function getAvailableItems($type = null)
    {
        return ArrayHelper::map(
            $this->getAuthManager()->getItems($type),
            'name',
            function ($item) {
                return empty($item->description)
                    ? $item->name
                    : $item->name . ' (' . $item->description . ')';
            }
        );
    }
}
