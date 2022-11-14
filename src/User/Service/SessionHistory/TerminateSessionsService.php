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

class TerminateSessionsService implements TerminateSessionsServiceInterface
{
    protected $sessionIds;

    public function __construct(array $sessionIds)
    {
        $this->sessionIds = $sessionIds;
    }

    public function run()
    {
        $currentSessionId = session_id();
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_write_close();
        }

        foreach ($this->sessionIds as $sessionId) {
            if ($sessionId === $currentSessionId) {
                $currentSessionId = null;
            }

            session_id($sessionId);
            session_start();
            session_destroy();
        }

        if ($currentSessionId) {
            session_id($currentSessionId);
        }
        session_start();

        return true;
    }
}
