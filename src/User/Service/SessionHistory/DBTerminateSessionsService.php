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


use yii\web\DbSession;

class DBTerminateSessionsService implements TerminateSessionsServiceInterface
{
    protected $sessionIds;
    protected $dbSession;
    protected $fieldName;

    public function __construct(array $sessionIds, DbSession $dbSession, $fieldName = 'id')
    {
        $this->sessionIds = $sessionIds;
        $this->dbSession = $dbSession;
        $this->fieldName = $fieldName;
    }

    public function run()
    {
        if (in_array(session_id(), $this->sessionIds)) {
            session_write_close();
        }

        $this->dbSession->db->createCommand()->delete(
            $this->dbSession->sessionTable,
            [$this->fieldName => $this->sessionIds]
        )->execute();

        return true;
    }
}
