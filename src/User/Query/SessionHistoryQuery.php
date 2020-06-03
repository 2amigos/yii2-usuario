<?php

/*
 * This file is part of the 2amigos/yii2-usuario project.
 *
 * (c) 2amigOS! <http://2amigos.us/>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Da\User\Query;

use Da\User\Traits\ModuleAwareTrait;
use yii\db\ActiveQuery;
use Yii;

class SessionHistoryQuery extends ActiveQuery
{
    use ModuleAwareTrait;

    public function whereUserId($userId)
    {
        return $this->andWhere($this->getCondition()->byUser($userId));
    }

    public function whereActive()
    {
        return $this->andWhere(['IS NOT', 'session_id', null]);
    }

    public function whereInActive($userId)
    {
        return $this->andWhere($this->getCondition()->inactive($userId));
    }


    public function whereExpired($userId)
    {
        return $this->andWhere($this->getCondition()->expired($userId));
    }

    public function whereExpiredInActive($userId)
    {
        return $this->andWhere($this->getCondition()->expiredInactive($userId));
    }

    public function selectSessionId()
    {
        return $this->select(['session_id']);
    }

    public function whereUserSession($userId, $sessionId)
    {
        return $this->andWhere($this->getCondition()->byUserSession(
            $userId,
            $sessionId
        ));
    }

    public function whereCurrentUser()
    {
        return $this->andWhere($this->getCondition()->currentUserCondition());
    }

    public function oldestUpdatedTimeActiveSession($userId)
    {
        return $this->whereExpiredInActive($userId)
            ->select(['updated_at'])
            ->limit(1)
            ->offset($this->getModule()->numberSessionHistory)
            ->orderBy(['updated_at' => SORT_DESC])->scalar();
    }

    /**
     * @return SessionHistoryCondition
     */
    protected function getCondition()
    {
        return Yii::$container->get(SessionHistoryCondition::class);
    }
}
