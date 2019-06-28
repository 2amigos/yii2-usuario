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

class m000000_000006_add_two_factor_fields extends Migration
{
    public function safeUp()
    {
        $this->addColumn('{{%user}}', 'auth_tf_key', $this->string(16));
        $this->addColumn(
            '{{%user}}',
            'auth_tf_enabled',
            $this->boolean()->defaultValue(MigrationHelper::getBooleanValue($this->db->driverName))
        );
    }

    public function safeDown()
    {
        $this->dropColumn('{{%user}}', 'auth_tf_key');
        $this->dropColumn('{{%user}}', 'auth_tf_enabled');
    }
}
