<?php
namespace Da\User\Factory;

use Da\User\Model\Token;
use Da\User\Model\User;
use Da\User\Traits\ContainerTrait;
use Yii;


class TokenFactory
{

    /**
     * @param $userId
     *
     * @return Token
     */
    public static function makeConfirmationToken($userId)
    {
        $token =  self::make(Token::class, ['user_id' => $userId, 'type' => Token::TYPE_CONFIRMATION]);

        $token->save(false);

        return $token;

    }

    protected static function make($class, $params = [])
    {
        return Yii::$container->get($class, $params);
    }

}
