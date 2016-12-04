<?php
namespace Da\User\Factory;

use Da\User\Model\User;
use Da\User\Module;
use Da\User\Service\MailService;
use Da\User\Traits\ModuleTrait;
use Yii;

class MailFactory
{
    public static function makeWelcomeMailerService(User $user)
    {
        /** @var Module $module */
        $module = Yii::$app->getModule('user');
        $to = $user->email;
        $from = $module->mailParams['fromEmail'];
        $subject = $module->mailParams['welcomeMailSubject'];
        $params = [
            'user' => $user,
            'token' => null,
            'module' => $module,
            'showPassword' => false
        ];

        return static::makeMailerService($from, $to, $subject, 'welcome', $params);
    }

    /**
     * Builds a MailerService
     *
     * @param string $from
     * @param string $to
     * @param string $subject
     * @param string $view
     * @param array $params
     *
     * @return MailService
     */
    public static function makeMailerService($from, $to, $subject, $view, array $params = [])
    {
        return Yii::$container->get(MailService::class, [$from, $to, $subject, $view, $params]);
    }
}
