<?php

/*
 * This file is part of the 2amigos/yii2-usuario project.
 *
 * (c) 2amigOS! <http://2amigos.us/>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Da\Usuario\Base\Helper;

use yii\base\Exception;
use yii\base\Security as YiiSecurity;

class Security
{
    /**
     * @var YiiSecurity
     */
    protected $security;

    public function __construct(YiiSecurity $security)
    {
        $this->security = $security;
    }

    /**
     * Generates a secure hash from a password and a random salt.
     *
     * @param string   $password
     * @param null|int $cost
     *
     * @throws Exception
     * @return string
     *
     */
    public function generatePasswordHash(string $password, int $cost = null): string
    {
        return $this->security->generatePasswordHash($password, $cost);
    }

    /**
     * Generates a random string
     *
     * @param int $length
     *
     * @throws Exception
     * @return string
     *
     */
    public function generateRandomString(int $length = 32): string
    {
        return $this->security->generateRandomString($length);
    }

    /**
     * @param  string $password
     * @param  string $hash
     * @return bool
     */
    public function validatePassword(string $password, string $hash): bool
    {
        return $this->security->validatePassword($password, $hash);
    }

    /**
     * @param  int    $length
     * @return string
     */
    public function generatePassword(int $length): string
    {
        $sets = [
            'abcdefghjkmnpqrstuvwxyz',
            'ABCDEFGHJKMNPQRSTUVWXYZ',
            '23456789',
        ];
        $all = '';
        $password = '';
        foreach ($sets as $set) {
            $password .= $set[array_rand(str_split($set))];
            $all .= $set;
        }

        $all = str_split($all);
        for ($i = 0; $i < $length - count($sets); ++$i) {
            $password .= $all[array_rand($all)];
        }

        $password = str_shuffle($password);

        return $password;
    }
}
