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

use Da\User\Model\User;
use Da\User\Query\UserQuery;
use Da\User\Service\ResetPasswordService;
use Da\User\Traits\ContainerAwareTrait;
use Yii;
use yii\base\InvalidConfigException;
use yii\base\Module;
use yii\console\Controller;
use yii\helpers\Console;

class PasswordController extends Controller
{
    use ContainerAwareTrait;

    protected $userQuery;

    public function __construct($id, Module $module, UserQuery $userQuery, array $config = [])
    {
        $this->userQuery = $userQuery;
        parent::__construct($id, $module, $config);
    }

    /**
     * This command updates the user's password.
     *
     * @param string $usernameOrEmail Username or email of the user who's password needs to be updated
     * @param string $password        The new password
     *
     * @throws InvalidConfigException
     */
    public function actionIndex($usernameOrEmail, $password)
    {
        /** @var User $user */
        $user = $this->userQuery->whereUsernameOrEmail($usernameOrEmail)->one();

        if ($user === null) {
            $this->stdout(Yii::t('usuario', 'User is not found') . "\n", Console::FG_RED);
        } else {
            if ($this->make(ResetPasswordService::class, [$password, $user])->run()) {
                $this->stdout(Yii::t('usuario', 'Password has been changed') . "\n", Console::FG_GREEN);
            } else {
                $this->stdout(Yii::t('usuario', 'Error occurred while changing password') . "\n", Console::FG_RED);
            }
        }
    }
}
