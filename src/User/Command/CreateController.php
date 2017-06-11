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

use Da\User\Factory\MailFactory;
use Da\User\Model\User;
use Da\User\Service\UserCreateService;
use Da\User\Traits\ContainerAwareTrait;
use Yii;
use yii\console\Controller;
use yii\helpers\Console;

class CreateController extends Controller
{
    use ContainerAwareTrait;

    public function actionIndex($email, $username, $password = null)
    {
        $user = $this->make(
            User::class,
            ['scenario' => 'create', 'email' => $email, 'username' => $username, 'password' => $password]
        );
        $mailService = MailFactory::makeWelcomeMailerService($user);

        if ($this->make(UserCreateService::class, [$user, $mailService])->run()) {
            $this->stdout(Yii::t('usuario', 'User has been created') . "!\n", Console::FG_GREEN);
        } else {
            $this->stdout(Yii::t('usuario', 'Please fix following errors:') . "\n", Console::FG_RED);
            foreach ($user->errors as $errors) {
                foreach ($errors as $error) {
                    $this->stdout(' - ' . $error . "\n", Console::FG_RED);
                }
            }
        }
    }
}
