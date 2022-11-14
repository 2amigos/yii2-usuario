<?php

/*
 * This file is part of the 2amigos/yii2-usuario project.
 *
 * (c) 2amigOS! <http://2amigos.us/>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Da\User\Migration\Session;

use Da\User\Helper\MigrationHelper;
use yii\db\Migration;

class m000000_000001_create_session_history_table extends Migration
{
    const SESSION_HISTORY_TABLE = '{{%session_history}}';
    const USER_TABLE = '{{%user}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable(self::SESSION_HISTORY_TABLE, [
            'user_id' => $this->integer(),
            'session_id' => $this->string()->null(),
            'user_agent' => $this->string()->notNull(),
            'ip' => $this->string(45)->notNull(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ]);

        $this->createIndex(
            '{{%session_history_user_id}}',
            self::SESSION_HISTORY_TABLE,
            ['user_id']
        );

        $this->createIndex(
            '{{%session_history_session_id}}',
            self::SESSION_HISTORY_TABLE,
            ['session_id']
        );

        $this->createIndex(
            '{{%session_history_updated_at}}',
            self::SESSION_HISTORY_TABLE,
            ['updated_at']
        );

        $this->addForeignKey(
            '{{%fk_user_session_history}}',
            self::SESSION_HISTORY_TABLE,
            'user_id',
            self::USER_TABLE,
            'id',
            'CASCADE',
            MigrationHelper::isMicrosoftSQLServer($this->db->driverName) ? 'NO ACTION' : 'RESTRICT'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable(self::SESSION_HISTORY_TABLE);
    }
}
