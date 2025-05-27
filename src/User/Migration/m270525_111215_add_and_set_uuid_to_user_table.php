<?php

namespace Da\User\Migration;

use Ramsey\Uuid\Uuid;
use yii\db\Migration;
use yii\db\Query;

/**
 * To prevent braking already existing migration with uuid, we broke from the "naming convention"
 */
class m270525_111215_add_and_set_uuid_to_user_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // Only create and set uuid if the column is not already existing
        if (!$this->getDb()->getTableSchema('{{%user}}')?->getColumn('uuid')) {
            // Add uuid column as nullable as there might be records in the table already
            $this->addColumn('{{%user}}', 'uuid', $this->string(36)->null()->unique()->after('id'));

            // Update all user records where the uuid column is empty. Maybe there already is an uuid column
            $userIds = (new Query())->select('id')->from('{{%user}}')->where([
                'OR',
                ['uuid' => null],
                ['uuid' => '']
            ])->column($this->getDb());
            foreach ($userIds as $userId) {
                $this->update('{{%user}}', ['uuid' => Uuid::uuid4()->toString()], ['id' => $userId]);
            }

            // Now, as all records are updated, make the column not nullable
            $this->alterColumn('{{%user}}', 'uuid', $this->string(36)->notNull());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo 'Migration ' . __CLASS__ . ' cannot be reverted.' . PHP_EOL;
    }
}
