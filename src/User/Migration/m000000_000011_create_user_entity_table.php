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

/**
 * Handles the creation of the table `{{%user_entity}}` that allows user to use passkeys for logging in.
 */
class m000000_000011_create_user_entity_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%user_entity}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'credential_id' => $this->string()->notNull(),
            'public_key' => $this->text()->notNull(),
            'sign_count' => $this->bigInteger()->notNull()->defaultValue(0),
            'type' => $this->string(32)->notNull(),
            'attestation_format' => $this->string(64)->notNull(),
            'device_id' => $this->string(128)->null(),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'last_used_at' => $this->timestamp()->null(),
            'name' => $this->string(128)->null(),
        ]);

        $this->addForeignKey(
            'fk_user_entity_user',
            '{{%user_entity}}',
            'user_id',
            '{{%user}}',
            'id',
            'CASCADE',
            'RESTRICT'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk_user_entity_user', '{{%user_entity}}');
        $this->dropTable('{{%user_entity}}');
    }
}
