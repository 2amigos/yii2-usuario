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

use Da\User\Model\SessionHistory;
use Da\User\Traits\AuthManagerAwareTrait;
use Da\User\Traits\ContainerAwareTrait;
use Yii;
use yii\base\InvalidConfigException;
use yii\base\InvalidParamException;
use yii\base\Widget;
use yii\helpers\ArrayHelper;

class SessionStatusWidget extends Widget
{
    use ContainerAwareTrait;
    use AuthManagerAwareTrait;

    /**
     * @var SessionHistory
     */
    public $model;

    /**
     * {@inheritdoc}
     *
     * @throws InvalidConfigException
     */
    public function init()
    {
        parent::init();
        if (!$this->model instanceof SessionHistory) {
            throw new InvalidConfigException(
                __CLASS__ . '::$userId should be instanceof ' . SessionHistory::class
            );
        }
    }

    /**
     * {@inheritdoc}
     *
     * @throws InvalidParamException
     */
    public function run()
    {
        if ($this->model->getIsActive()) {
            if ($this->model->session_id === Yii::$app->session->id) {
                $value = Yii::t('usuario', 'Current');
            } else {
                $value = Yii::t('usuario', 'Active');
            }
        } else {
            $value = Yii::t('usuario', 'Inactive');
        }

        return $value;
    }

    /**
     * Returns available auth items to be attached to the user.
     *
     * @param int|null type of auth items or null to return all
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
