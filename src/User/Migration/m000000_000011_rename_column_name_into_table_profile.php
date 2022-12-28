<?php

use yii\db\Migration;

/**
 * Class m000000_000011_rename_column_name_into_table_profile
 */
class m000000_000011_rename_column_name_into_table_profile extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->renameColumn('{{%profile}}', 'name', 'firstname');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->renameColumn('{{%profile}}', 'firstname', 'name');
    }
}
