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

use Da\User\Model\Token;
use Yii;
use yii\base\InvalidConfigException;

class TokenFactory
{
    /**
     * @param $userId
     *
     * @throws InvalidConfigException
     * @return Token
     */
    public static function makeConfirmationToken($userId)
    {
        $token = self::make($userId, Token::TYPE_CONFIRMATION);

        $token->save(false);

        return $token;
    }

    /**
     * @param $userId
     *
     * @return Token
     */
    public static function makeConfirmNewMailToken($userId)
    {
        $token = self::make($userId, Token::TYPE_CONFIRM_NEW_EMAIL);

        $token->save(false);

        return $token;
    }

    /**
     * @param $userId
     *
     * @throws InvalidConfigException
     * @return Token
     */
    public static function makeConfirmOldMailToken($userId)
    {
        $token = self::make($userId, Token::TYPE_CONFIRM_OLD_EMAIL);

        $token->save(false);

        return $token;
    }

    /**
     * @param $userId
     *
     * @throws InvalidConfigException
     * @return Token
     */
    public static function makeRecoveryToken($userId)
    {
        $token = self::make($userId, Token::TYPE_RECOVERY);

        $token->save(false);

        return $token;
    }

    /**
     * @param $userId
     * @param $type
     *
     * @throws InvalidConfigException
     * @return Token|\object
     */
    protected static function make($userId, $type)
    {
        return Yii::createObject(['class' => Token::class, 'user_id' => $userId, 'type' => $type]);
    }
}
