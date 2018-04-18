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

class m000000_000005_add_last_login_at extends Migration
{
    public function safeUp()
    {
        /* if it already exists do not add the field */
        $schema = $this->db->getTableSchema('{{%user}}');
        if (!in_array('last_login_at',$schema->columnNames)) {
            $this->addColumn('{{%user}}', 'last_login_at', $this->integer());
        }
    }

    public function safeDown()
    {
        $this->dropColumn('{{%user}}', 'last_login_at');
    }
}
