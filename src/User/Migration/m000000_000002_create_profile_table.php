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

class m000000_000002_create_profile_table extends Migration
{
    public function safeUp()
    {
        $this->createTable(
            '{{%profile}}',
            [
                'user_id' => $this->integer()->notNull(),
                'name' => $this->string(255),
                'public_email' => $this->string(255),
                'gravatar_email' => $this->string(255),
                'gravatar_id' => $this->string(32),
                'location' => $this->string(255),
                'website' => $this->string(255),
                'timezone' => $this->string(40),
                'bio' => $this->text(),
            ],
            MigrationHelper::resolveTableOptions($this->db->driverName)
        );

        $this->addPrimaryKey('{{%profile_pk}}', '{{%profile}}', 'user_id');

        $restrict = MigrationHelper::isMicrosoftSQLServer($this->db->driverName) ? 'NO ACTION' : 'RESTRICT';

        $this->addForeignKey('fk_profile_user', '{{%profile}}', 'user_id', '{{%user}}', 'id', 'CASCADE', $restrict);
    }

    public function safeDown()
    {
        $this->dropTable('{{%profile}}');
    }
}
