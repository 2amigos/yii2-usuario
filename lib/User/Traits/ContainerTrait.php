<?php

namespace Da\User\Traits;

use Da\User\Helper\AuthHelper;
use Da\User\Helper\ClassMapHelper;
use Yii;
use yii\di\Container;

/**
 * @property-read Container $di
 * @property-ready Da\User\Helper\AuthHelper $auth
 * @property-ready Da\User\Helper\ClassMapHelper $classMap
 */
trait ContainerTrait
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
     * @return object
     */
    public function make($class, $params = [], $config = [])
    {
        return $this->getDi()->get($class, $params, $config);
    }

    /**
     * @return \Da\User\Helper\AuthHelper
     */
    public function getAuth()
    {
        return $this->getDi()->get(AuthHelper::class);
    }

    /**
     * @return \Da\User\Helper\ClassMapHelper
     */
    public function getClassMap()
    {
        return $this->getDi()->get(ClassMapHelper::class);
    }
}
