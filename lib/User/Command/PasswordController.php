<?php

namespace Da\User\Command;

use Da\User\Model\User;
use Da\User\Query\UserQuery;
use Da\User\Service\ResetPasswordService;
use Da\User\Traits\ContainerTrait;
use Yii;
use yii\base\Module;
use yii\console\Controller;
use yii\helpers\Console;

class PasswordController extends Controller
{
    use ContainerTrait;

    protected $userQuery;

    public function __construct($id, Module $module, UserQuery $userQuery, array $config)
    {
        $this->userQuery = $userQuery;
        parent::__construct($id, $module, $config);
    }

    public function actionIndex($usernameOrEmail, $password)
    {
        /** @var User $user */
        $user = $this->userQuery->whereUsernameOrEmail($usernameOrEmail)->one();

        if ($user === null) {
            $this->stdout(Yii::t('user', 'User is not found')."\n", Console::FG_RED);
        } else {
            if ($this->make(ResetPasswordService::class, [$password, $user])->run()) {
                $this->stdout(Yii::t('user', 'Password has been changed')."\n", Console::FG_GREEN);
            } else {
                $this->stdout(Yii::t('user', 'Error occurred while changing password')."\n", Console::FG_RED);
            }
        }
    }
}
