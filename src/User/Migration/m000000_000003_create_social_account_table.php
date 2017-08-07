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

class m000000_000003_create_social_account_table extends Migration
{
    public function safeUp()
    {
        $this->createTable(
            '{{%social_account}}',
            [
                'id' => $this->primaryKey(),
                'user_id' => $this->integer(),
                'provider' => $this->string(255)->notNull(),
                'client_id' => $this->string(255)->notNull(),
                'code' => $this->string(32),
                'email' => $this->string(255),
                'username' => $this->string(255),
                'data' => $this->text(),
                'created_at' => $this->integer(),
            ],
            MigrationHelper::resolveTableOptions($this->db->driverName)
        );

        $this->createIndex(
            'idx_social_account_provider_client_id',
            '{{%social_account}}',
            ['provider', 'client_id'],
            true
        );

        $this->createIndex('idx_social_account_code', '{{%social_account}}', 'code', true);

        $this->addForeignKey(
            'fk_social_account_user',
            '{{%social_account}}',
            'user_id',
            '{{%user}}',
            'id',
            'CASCADE',
            (MigrationHelper::isMicrosoftSQLServer($this->db->driverName) ? 'NO ACTION' : 'RESTRICT')
        );
    }

    public function safeDown()
    {
        $this->dropTable('{{%social_account}}');
    }
}
