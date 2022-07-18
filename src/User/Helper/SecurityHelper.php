<?php

/*
 * This file is part of the 2amigos/yii2-usuario project.
 *
 * (c) 2amigOS! <http://2amigos.us/>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Da\User\Helper;

use Yii;
use yii\base\Exception;
use yii\base\InvalidConfigException;
use yii\base\Security;

class SecurityHelper
{
    /**
     * @var Security
     */
    protected $security;

    public function __construct(Security $security)
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
    public function generatePasswordHash($password, $cost = null)
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
    public function generateRandomString($length = 32)
    {
        return $this->security->generateRandomString($length);
    }

    public function validatePassword($password, $hash)
    {
        return $this->security->validatePassword($password, $hash);
    }

    public function generatePassword($length, $minPasswordRequirements = null)
    {
        $sets = [
            'lower' => 'abcdefghjkmnpqrstuvwxyz',
            'upper' => 'ABCDEFGHJKMNPQRSTUVWXYZ',
            'digit' => '123456789',
            'special' => '!#$%&*+,-.:;<=>?@_~'
        ];
        $all = '';
        $password = '';

        if (!isset($minPasswordRequirements)) {
            if (isset(Yii::$app->getModule('user')->minPasswordRequirements)) {
                $minPasswordRequirements = Yii::$app->getModule('user')->minPasswordRequirements;
            } else {
                $minPasswordRequirements = [
                    'lower' => 1,
                    'digit' => 1,
                    'upper' => 1,
                ];
            }
        }
        if (isset($minPasswordRequirements['min']) && $length < $minPasswordRequirements['min']) {
            $length = $minPasswordRequirements['min'];
        }
        foreach ($sets as $setKey => $set) {
            if (isset($minPasswordRequirements[$setKey])) {
                for ($i = 0; $i < $minPasswordRequirements[$setKey]; $i++) {
                    $password .= $set[array_rand(str_split($set))];
                }
            }
            $all .= $set;
        }
        $passwordLength = strlen($password);
        if ($passwordLength > $length) {
            throw new InvalidConfigException('The minimum length is incompatible with other minimum requirements.');
        }
        $all = str_split($all);
        for ($i = 0; $i < $length - $passwordLength; ++$i) {
            $password .= $all[array_rand($all)];
        }
        $password = str_shuffle($password);

        return $password;
    }
}
