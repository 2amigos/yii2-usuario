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
