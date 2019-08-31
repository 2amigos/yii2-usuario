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

class m000000_000009_add_gdpr_consent_fields extends Migration
{
    public function safeUp()
    {
        $this->addColumn('{{%user}}', 'gdpr_consent', $this->boolean()->defaultValue(MigrationHelper::getBooleanValue($this->db->driverName, false)));
        $this->addColumn('{{%user}}', 'gdpr_consent_date', $this->integer(11)->null());
        $this->addColumn('{{%user}}', 'gdpr_deleted', $this->boolean()->defaultValue(MigrationHelper::getBooleanValue($this->db->driverName, false)));
    }

    public function safeDown()
    {
        $this->dropColumn('{{%user}}', 'gdpr_consent');
        $this->dropColumn('{{%user}}', 'gdpr_consent_date');
        $this->dropColumn('{{%user}}', 'gdpr_deleted');
    }
}
