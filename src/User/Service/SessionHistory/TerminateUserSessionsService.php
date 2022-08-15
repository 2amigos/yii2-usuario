<?php

/*
 * This file is part of the 2amigos/yii2-usuario project.
 *
 * (c) 2amigOS! <http://2amigos.us/>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Da\User\Service\SessionHistory;

use Da\User\Contracts\ServiceInterface;
use Da\User\Event\SessionEvent;
use Da\User\Model\SessionHistory;
use Da\User\Model\User;
use Da\User\Traits\ContainerAwareTrait;
use Da\User\Traits\ModuleAwareTrait;
use Yii;
use yii\web\Session;

class TerminateUserSessionsService implements ServiceInterface
{
    use ContainerAwareTrait;
    use ModuleAwareTrait;

    protected $userId;
    protected $session;
    protected $excludeCurrentSession;

    public function __construct($userId, Session $session, $excludeCurrentSession = true)
    {
        $this->userId = intval($userId);
        $this->session = $session;
        $this->excludeCurrentSession = $excludeCurrentSession;
    }

    public function run()
    {
        $user = $this->getUser($this->userId);
        $sessionIds = $this->getSessionIds($user->id);

        Yii::$app->db->transaction(function () use ($sessionIds, $user) {
            /** @var SessionEvent $event */
            $event = $this->make(SessionEvent::class, [$user]);

            $user->trigger(SessionEvent::EVENT_BEFORE_TERMINATE_USER_SESSIONS, $event);

            $this->make(TerminateSessionsServiceInterface::class, [$sessionIds])->run();

            $user->updateAttributes([
                'auth_key' => Yii::$app->security->generateRandomString(),
            ]);

            if ($this->excludeCurrentUser()) {
                Yii::$app->user->switchIdentity(
                    $user,
                    $this->getModule()->rememberLoginLifespan
                );
            }

            $user->trigger(SessionEvent::EVENT_AFTER_TERMINATE_USER_SESSIONS, $event);
        });

        return true;
    }

    /**
     * @param  int  $userId
     * @return User
     */
    protected function getUser($userId)
    {
        return ($this->make(User::class))::findOne($userId);
    }

    /**
     * @param $userId
     * @return int[]
     */
    protected function getSessionIds($userId)
    {
        /** @var SessionHistory $sessionHistory */
        $sessionHistory = $this->make(SessionHistory::class);
        $sessionIds = $sessionHistory::find()->whereUserId($userId)->whereActive()->selectSessionId()->column();

        if ($this->excludeCurrentUser()) {
            foreach ($sessionIds as $key => $sessionId) {
                if ($sessionId === $this->session->id) {
                    unset($sessionIds[$key]);
                    break;
                }
            }
        }

        return $sessionIds;
    }

    protected function excludeCurrentUser()
    {
        return $this->excludeCurrentSession && $this->userId === Yii::$app->user->id;
    }
}
