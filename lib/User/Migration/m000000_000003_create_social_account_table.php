<?php

namespace Da\User\Migration;

use yii\db\Migration;

class m000000_000003_create_social_account_table extends Migration
{
    public function up()
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
            ]
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
            'RESTRICT'
        );
    }

    public function down()
    {
        $this->dropTable('{{%social_account}}');
    }
}
