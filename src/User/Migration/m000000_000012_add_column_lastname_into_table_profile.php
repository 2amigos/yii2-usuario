<?php

use yii\db\Migration;

/**
 * Class m000000_000012_add_column_lastname_into_table_profile.php
 */
class m000000_000012_add_column_lastname_into_table_profile extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%profile}}', 'lastname', $this->string()->after('firstname'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%profile}}', 'lastname');
    }
}
