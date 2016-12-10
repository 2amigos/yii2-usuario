<?php


class m000000_000001_create_user_table extends \yii\db\Migration
{
    public function up()
    {
        $this->createTable('{{%user}}', [
            'id' => $this->primaryKey(),
            'username' => $this->string(255)->notNull(),
            'email' => $this->string(255)->notNull(),
            'password_hash' => $this->string(60)->notNull(),
            'auth_key' => $this->string(32)->notNull(),
            'unconfirmed_email' => $this->string(255),
            'registration_id' => $this->string(45),
            'confirmed_at' => $this->integer(),
            'blocked_at' => $this->integer(),
            'updated_at' => $this->integer()->notNull(),
            'created_at' =>$this->integer()->notNull()
        ]);


        $this->createIndex('idx_user_username', '{{%user}}', 'username', true);
        $this->createIndex('idx_user_email', '{{%user}}', 'email', true);
    }

    public function down()
    {
        $this->dropTable('{{%user}}');
    }
}
