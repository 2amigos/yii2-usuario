<?php

/*
 * This file is part of the 2amigos/yii2-usuario project.
 *
 * (c) 2amigOS! <http://2amigos.us/>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Da\User\Migration;

use Da\User\Helper\MigrationHelper;
use yii\db\Migration;

class m000000_000001_create_user_table extends Migration
{
    public function safeUp()
    {
        $this->createTable(
            '{{%user}}',
            [
                'id' => $this->primaryKey(),
                'username' => $this->string(255)->notNull(),
                'email' => $this->string(255)->notNull(),
                'password_hash' => $this->string(60)->notNull(),
                'auth_key' => $this->string(32)->notNull(),
                'unconfirmed_email' => $this->string(255),
                'registration_ip' => $this->string(45),
                'flags' => $this->integer()->notNull()->defaultValue('0'),
                'confirmed_at' => $this->integer(),
                'blocked_at' => $this->integer(),
                'updated_at' => $this->integer()->notNull(),
                'created_at' => $this->integer()->notNull(),
            ],
            MigrationHelper::resolveTableOptions($this->db->driverName)
        );

        $this->createIndex('idx_user_username', '{{%user}}', 'username', true);
        $this->createIndex('idx_user_email', '{{%user}}', 'email', true);
    }

    public function safeDown()
    {
        $this->dropTable('{{%user}}');
    }
}
