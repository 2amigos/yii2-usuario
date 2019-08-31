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

use Da\User\Query\UserQuery;
use Da\User\Service\UserConfirmationService;
use Da\User\Traits\ContainerAwareTrait;
use Yii;
use yii\base\Module;
use yii\console\Controller;
use yii\helpers\Console;

class ConfirmController extends Controller
{
    use ContainerAwareTrait;

    protected $userQuery;

    public function __construct($id, Module $module, UserQuery $userQuery, array $config = [])
    {
        $this->userQuery = $userQuery;

        parent::__construct($id, $module, $config);
    }

    /**
     * Confirms a a user by setting its field `confirmed_at` to current time.
     *
     * @param string $usernameOrEmail Username or email of the user
     *
     * @throws \yii\base\InvalidConfigException
     */
    public function actionIndex($usernameOrEmail)
    {
        $user = $this->userQuery->whereUsernameOrEmail($usernameOrEmail)->one();
        if ($user === null) {
            $this->stdout(Yii::t('usuario', 'User is not found') . "\n", Console::FG_RED);
        } elseif ($this->make(UserConfirmationService::class, [$user])->run()) {
            $this->stdout(Yii::t('usuario', 'User has been confirmed') . "\n", Console::FG_GREEN);
        } else {
            $this->stdout(Yii::t('usuario', 'Error occurred while confirming user') . "\n", Console::FG_RED);
        }
    }
}
