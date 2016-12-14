<?php

namespace Da\User\Helper;

/**
 * ModelMapHelper.php.
 *
 * Date: 3/12/16
 * Time: 18:10
 *
 * @author Antonio Ramirez <hola@2amigos.us>
 */
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
     * @return mixed
     *
     * @throws \Exception
     */
    public function get($key)
    {
        if (array_key_exists($key, $this->map)) {
            return $this->map[$key];
        }
        throw new \Exception('Unknown model map key: '.$key);
    }
}
