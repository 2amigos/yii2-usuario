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

use Da\User\Contracts\MailChangeStrategyInterface;
use Da\User\Form\SettingsForm;
use Da\User\Strategy\DefaultEmailChangeStrategy;
use Da\User\Strategy\InsecureEmailChangeStrategy;
use Da\User\Strategy\SecureEmailChangeStrategy;
use Exception;
use Yii;
use yii\base\InvalidParamException;

class EmailChangeStrategyFactory
{
    protected static $map = [
        MailChangeStrategyInterface::TYPE_INSECURE => InsecureEmailChangeStrategy::class,
        MailChangeStrategyInterface::TYPE_DEFAULT => DefaultEmailChangeStrategy::class,
        MailChangeStrategyInterface::TYPE_SECURE => SecureEmailChangeStrategy::class,
    ];

    /**
     * @param $strategy
     * @param SettingsForm $form
     *
     * @throws Exception
     * @return MailChangeStrategyInterface
     *
     */
    public static function makeByStrategyType($strategy, SettingsForm $form)
    {
        if (array_key_exists($strategy, static::$map)) {
            return Yii::$container->get(static::$map[$strategy], [$form]);
        }

        throw new InvalidParamException('Unknown strategy type');
    }

    /**
     * @param SettingsForm $form
     *
     * @return DefaultEmailChangeStrategy
     */
    public static function makeDefaultEmailChangeStrategy(SettingsForm $form)
    {
        return Yii::$container->get(static::$map[MailChangeStrategyInterface::TYPE_DEFAULT], [$form]);
    }

    /**
     * @param SettingsForm $form
     *
     * @return InsecureEmailChangeStrategy
     */
    public static function makeInsecureEmailChangeStrategy(SettingsForm $form)
    {
        return Yii::$container->get(static::$map[MailChangeStrategyInterface::TYPE_INSECURE], [$form]);
    }

    /**
     * @param SettingsForm $form
     *
     * @return SecureEmailChangeStrategy
     */
    public static function makeSecureEmailChangeStrategy(SettingsForm $form)
    {
        return Yii::$container->get(static::$map[MailChangeStrategyInterface::TYPE_SECURE], [$form]);
    }
}
