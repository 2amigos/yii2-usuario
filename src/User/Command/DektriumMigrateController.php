<?php

/*
 * This file is part of the 2amigos/yii2-usuario project.
 *
 * (c) 2amigOS! <http://2amigos.us/>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Da\User\Command;

use Yii;
use yii\console\Controller;
use yii\helpers\Console;


class DektriumMigrateController extends Controller
{

    private $migrations = [
            'm000000_000001_create_user_table',
            'm000000_000002_create_profile_table',
            'm000000_000003_create_social_account_table',
            'm000000_000004_create_token_table',
            'm000000_000005_add_last_login_at',
    ];

    /** @var String $migrationTable */
    public function actionIndex($migrationTable = '{{%migration}}',$namespace = '')
    {
        /* try get migrationPath */
        if ($namespace==''  and isset(Yii::$app->controllerMap['migrate']) and isset(Yii::$app->controllerMap['migrate']['migrationNamespaces'])) {
            if (in_array('Da\\User\\Migration',Yii::$app->controllerMap['migrate']['migrationNamespaces'])) {
                $namespace='Da\\User\\Migration\\';
            }
        }
        $cmd = Yii::$app->db->createCommand('INSERT INTO '.$migrationTable.' VALUES(:Migration,:Time)');
        foreach ($this->migrations as $m) {
            $cmd->bindValue(':Migration',$namespace.$m);
            $cmd->bindValue(':Time',time());
            $cmd->execute();
        }
        $this->stdout(Yii::t('usuario', 'Migrations has been applied') . "\n", Console::FG_GREEN);
    }

}
