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
 * Handles adding columns to table `{{%user}}`.
 */
class m000000_000010_add_auth_tf_type_auth_tf_mobile_phone_columns_to_user_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%user}}', 'auth_tf_type', $this->string(20)->after('auth_tf_enabled')->null());
        $this->addColumn('{{%user}}', 'auth_tf_mobile_phone', $this->string(20)->after('auth_tf_type')->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%user}}', 'auth_tf_type');
        $this->dropColumn('{{%user}}', 'auth_tf_mobile_phone');
    }
}
