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

use yii\db\Migration;

class m000000_000004_create_token_table extends Migration
{
    public function up()
    {
        $this->createTable(
            '{{%token}}',
            [
                'user_id' => $this->integer(),
                'code' => $this->string(32)->notNull(),
                'type' => $this->smallInteger(6)->notNull(),
                'created_at' => $this->integer()->notNull(),
            ]
        );

        $this->createIndex('idx_token_user_id_code_type', '{{%token}}', ['user_id', 'code', 'type'], true);

        $this->addForeignKey('fk_token_user', '{{%token}}', 'user_id', '{{%user}}', 'id', 'CASCADE', 'RESTRICT');
    }

    public function down()
    {
        $this->dropTable('{{%token}}');
    }
}
