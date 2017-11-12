<?php

/*
 * This file is part of the 2amigos/yii2-usuario project.
 *
 * (c) 2amigOS! <http://2amigos.us/>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Da\User\Traits;

use Da\User\Helper\AuthHelper;
use Da\User\Helper\ClassMapHelper;
use Yii;
use yii\base\InvalidConfigException;
use yii\di\Container;

/**
 * @property-read Container $di
 * @property-ready Da\User\Helper\AuthHelper $auth
 * @property-ready Da\User\Helper\ClassMapHelper $classMap
 */
trait ContainerAwareTrait
{
    /**
     * @return Container
     */
    public function getDi()
    {
        return Yii::$container;
    }

    /**
     * Gets a class from the container.
     *
     * @param string $class  he class name or an alias name (e.g. `foo`) that was previously registered via [[set()]]
     *                       or [[setSingleton()]]
     * @param array  $params constructor parameters
     * @param array  $config attributes
     *
     * @throws InvalidConfigException
     * @return object
     */
    public function make($class, $params = [], $config = [])
    {
        return $this->getDi()->get($class, $params, $config);
    }

    /**
     * @throws InvalidConfigException
     * @return \Da\User\Helper\AuthHelper|object
     *
     */
    public function getAuth()
    {
        return $this->getDi()->get(AuthHelper::class);
    }

    /**
     * @throws InvalidConfigException
     * @return \Da\User\Helper\ClassMapHelper|object
     *
     */
    public function getClassMap()
    {
        return $this->getDi()->get(ClassMapHelper::class);
    }
}
