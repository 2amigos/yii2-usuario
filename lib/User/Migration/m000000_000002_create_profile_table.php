<?php
namespace Da\User\Migration;

use yii\db\Migration;

class m000000_000002_create_profile_table extends Migration
{
    public function up()
    {
        $this->createTable(
            '{{%profile}}',
            [
                'user_id' => $this->primaryKey(),
                'name' => $this->string(255),
                'public_email' => $this->string(255),
                'gravatar_email' => $this->string(255),
                'gravatar_id' => $this->string(32),
                'location' => $this->string(255),
                'website' => $this->string(255),
                'timezone' => $this->string(40),
                'bio' => $this->text()
            ]
        );

        $this->addForeignKey('fk_profile_user', '{{%profile}}', 'user_id', '{{%user}}', 'id', 'CASCADE', 'RESTRICT');
    }

    public function down()
    {
        $this->dropTable('{{%profile}}');
    }
}
