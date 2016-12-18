<?php

namespace Da\User\Command;

use Da\User\Query\UserQuery;
use Da\User\Service\UserConfirmationService;
use Da\User\Traits\ContainerTrait;
use Yii;
use yii\base\Module;
use yii\console\Controller;
use yii\helpers\Console;

class ConfirmController extends Controller
{
    use ContainerTrait;

    protected $userQuery;

    public function __construct($id, Module $module, UserQuery $userQuery, array $config)
    {
        $this->userQuery = $userQuery;

        parent::__construct($id, $module, $config);
    }

    public function actionIndex($usernameOrEmail)
    {
        $user = $this->userQuery->whereUsernameOrEmail($usernameOrEmail)->one();
        if ($user === null) {
            $this->stdout(Yii::t('user', 'User is not found')."\n", Console::FG_RED);
        } else {
            if ($this->make(UserConfirmationService::class, [$user])->run()) {
                $this->stdout(Yii::t('user', 'User has been confirmed')."\n", Console::FG_GREEN);
            } else {
                $this->stdout(Yii::t('user', 'Error occurred while confirming user')."\n", Console::FG_RED);
            }
        }
    }
}
