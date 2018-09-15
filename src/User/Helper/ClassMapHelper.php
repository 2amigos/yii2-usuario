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

use yii\base\InvalidArgumentException;

class ClassMapHelper
{
    protected $map = [];

    /**
     * ModelClassMapHelper constructor.
     *
     * @param array $map
     */
    public function __construct($map = [])
    {
        $this->map = $map;
    }

    /**
     * @param $key
     * @param $class
     */
    public function set($key, $class)
    {
        $this->map[$key] = $class;
    }

    /**
     * @param $key
     *
     * @throws \InvalidArgumentException
     * @return mixed
     *
     */
    public function get($key)
    {
        if (array_key_exists($key, $this->map)) {
            return $this->map[$key];
        }
        throw new InvalidArgumentException('Unknown model map key: ' . $key);
    }
}
