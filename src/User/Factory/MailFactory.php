<?php

/*
 * This file is part of the 2amigos/yii2-usuario project.
 *
 * (c) 2amigOS! <http://2amigos.us/>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Da\User\Factory;

use Da\User\Event\MailEvent;
use Da\User\Model\Token;
use Da\User\Model\User;
use Da\User\Module;
use Da\User\Service\MailService;
use Yii;
use yii\base\InvalidConfigException;

class MailFactory
{
    /**
     * @param User $user
     * @param bool $showPassword
     *
     * @throws InvalidConfigException
     * @return MailService
     */
    public static function makeWelcomeMailerService(User $user, $showPassword = false)
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
            'showPassword' => $showPassword,
        ];

        return static::makeMailerService(MailEvent::TYPE_WELCOME, $from, $to, $subject, 'welcome', $params);
    }

    /**
     * @param string $email
     * @param Token  $token
     *
     * @throws InvalidConfigException
     * @return MailService
     */
    public static function makeRecoveryMailerService($email, Token $token = null)
    {
        /** @var Module $module */
        $module = Yii::$app->getModule('user');
        $to = $email;
        $from = $module->mailParams['fromEmail'];
        $subject = $module->mailParams['recoveryMailSubject'];
        $params = [
            'user' => $token && $token->user ? $token->user : null,
            'token' => $token,
        ];

        return static::makeMailerService(MailEvent::TYPE_RECOVERY, $from, $to, $subject, 'recovery', $params);
    }

    /**
     * @param User       $user
     * @param Token|null $token
     *
     * @throws InvalidConfigException
     * @return MailService
     */
    public static function makeConfirmationMailerService(User $user, Token $token = null)
    {
        /** @var Module $module */
        $module = Yii::$app->getModule('user');
        $to = $user->email;
        $from = $module->mailParams['fromEmail'];
        $subject = $module->mailParams['confirmationMailSubject'];
        $params = [
            'user' => $token && $token->user ? $token->user : null,
            'token' => $token,
        ];

        return static::makeMailerService(MailEvent::TYPE_CONFIRM, $from, $to, $subject, 'confirmation', $params);
    }

    /**
     * @param User  $user
     * @param Token $token
     *
     * @throws InvalidConfigException
     * @return MailService
     */
    public static function makeReconfirmationMailerService(User $user, Token $token)
    {
        /** @var Module $module */
        $module = Yii::$app->getModule('user');
        $to = $token->type === Token::TYPE_CONFIRM_NEW_EMAIL
            ? $user->unconfirmed_email
            : $user->email;

        $from = $module->mailParams['fromEmail'];
        $subject = $module->mailParams['reconfirmationMailSubject'];
        $params = [
            'user' => $token && $token->user ? $token->user : null,
            'token' => $token,
        ];

        return static::makeMailerService(MailEvent::TYPE_RECONFIRM, $from, $to, $subject, 'reconfirmation', $params);
    }

    /**
     * Builds a MailerService.
     *
     * @param string                $type
     * @param string|array|\Closure $from
     * @param string                $to
     * @param string                $subject
     * @param string                $view
     * @param array                 $params
     *
     * @throws InvalidConfigException
     * @return MailService
     *
     */
    public static function makeMailerService($type, $from, $to, $subject, $view, $params = [])
    {
        if ($from instanceof \Closure) {
            $from = $from($type);
        }
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return Yii::$container->get(
            MailService::class,
            [$type, $from, $to, $subject, $view, $params, Yii::$app->getMailer()]
        );
    }
}
