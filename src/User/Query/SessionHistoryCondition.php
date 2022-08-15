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
use Yii;
use yii\web\Session;

class SessionHistoryCondition
{
    use ModuleAwareTrait;

    private $session;

    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    public function unbindSession()
    {
        return ['session_id' => null];
    }

    public function bySession($sessionId)
    {
        return ['session_id' => $sessionId];
    }

    public function byUser($userId)
    {
        return [
            'user_id' => $userId,
        ];
    }

    public function byUserSession($userId, $sessionId)
    {
        return [
            'user_id' => $userId,
            'session_id' => $sessionId,
        ];
    }

    public function inactive($userId = null)
    {
        $where = [
            'AND',
            ['session_id' => null]
        ];

        if (isset($userId)) {
            $where[] = $this->byUser($userId);
        }

        return $where;
    }

    public function expired($userId = null)
    {
        $where = [
            'AND',
            ['<', 'updated_at', $this->getExpiredTime()]
        ];

        if (isset($userId)) {
            $where[] = $this->byUser($userId);
        }

        return $where;
    }

    public function expiredInactive($userId = null)
    {
        return [
            'OR',
            $this->expired($userId),
            $this->inactive($userId),
        ];
    }

    public function shouldDeleteBefore($updatedAt, $userId)
    {
        $condition = ['<', 'updated_at', $updatedAt];
        if ($updatedAt > $this->getExpiredTime()) {
            $condition = [
                'OR',
                [
                    'AND',
                    $this->inactive(),
                    $condition,
                ],
                $this->expired()
            ];
        }

        return [
            'AND',
            $this->byUser($userId),
            $condition,
        ];
    }

    /**
     * @return int
     */
    public function getExpiredTime()
    {
        $module = $this->getModule();
        $time = time() - max($module->rememberLoginLifespan, $this->session->getTimeout());
        if (false === $module->hasTimeoutSessionHistory()) {
            return $time;
        }

        return $time - $module->timeoutSessionHistory;
    }

    public function inactiveData()
    {
        return [
            'session_id' => null,
        ];
    }

    /**
     * @return array
     */
    public function currentUserData()
    {
        return [
            'user_id' => Yii::$app->user->id,
            'session_id' => Yii::$app->session->getId(),
            'user_agent' => Yii::$app->request->userAgent,
            'ip' => Yii::$app->request->userIP,
        ];
    }

    /**
     * @return array
     */
    public function currentUserCondition()
    {
        return [
            'user_id' => Yii::$app->user->id,
            'session_id' => Yii::$app->session->getId(),
            'user_agent' => Yii::$app->request->userAgent,
        ];
    }
}
