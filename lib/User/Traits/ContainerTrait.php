<?php

namespace Da\User\Traits;

use Yii;
use yii\di\Container;

/**
 * @property-read Container $di
 * @property-ready Da\User\Helper\AuthHelper $authHelper
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
     * @param string $class he class name or an alias name (e.g. `foo`) that was previously registered via [[set()]]
     * or [[setSingleton()]].
     * @param array $params
     *
     * @return object
     */
    public function make($class, $params = [])
    {
        return $this->getDi()->get($class, $params);
    }

    /**
     * @return \Da\User\Helper\AuthHelper
     */
    public function getAuthHelper()
    {
        return Yii::$container->get('Da\User\Helper\AuthHelper');
    }

    /**
     * @return \Da\User\Helper\ClassMapHelper
     */
    public function getClassMapHelper()
    {
        return Yii::$container->get('Da\User\Helper\ClassMapHelper');
    }

}
