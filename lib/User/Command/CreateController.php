<?php

namespace Da\User\Command;

use Da\User\Factory\MailFactory;
use Da\User\Model\User;
use Da\User\Service\UserCreateService;
use Da\User\Traits\ContainerTrait;
use yii\console\Controller;
use Yii;
use yii\helpers\Console;

class CreateController extends Controller
{
    use ContainerTrait;

    public function actionIndex($email, $username, $password = null)
    {
        $user = $this->make(
            User::class,
            ['scenario' => 'create', 'email' => $email, 'username' => $username, 'password' => $password]
        );
        $mailService = MailFactory::makeWelcomeMailerService($user);

        if ($this->make(UserCreateService::class, [$user, $mailService])->run()) {
            $this->stdout(Yii::t('user', 'User has been created')."!\n", Console::FG_GREEN);
        } else {
            $this->stdout(Yii::t('user', 'Please fix following errors:')."\n", Console::FG_RED);
            foreach ($user->errors as $errors) {
                foreach ($errors as $error) {
                    $this->stdout(' - '.$error."\n", Console::FG_RED);
                }
            }
        }
    }
}
