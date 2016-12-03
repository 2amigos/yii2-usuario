<?php
namespace Da\User;

use Da\User\Helper\AuthHelper;
use Da\User\Query\AccountQuery;
use Da\User\Query\ProfileQuery;
use Da\User\Query\TokenQuery;
use Da\User\Query\UserQuery;
use yii\base\Application;
use yii\base\BootstrapInterface;
use Yii;

/**
 *
 * Bootstrap.php
 *
 * Date: 3/12/16
 * Time: 15:13
 * @author Antonio Ramirez <hola@2amigos.us>
 */
class Bootstrap implements BootstrapInterface
{
    public function bootstrap($app)
    {
        if($app->hasModule('user') && $app->getModule('user') instanceof Module) {

            // configure yii's container
            $this->setContainer();
        }
    }

    protected function setContainer()
    {
        $di = Yii::$container;
        // helpers
        $di->set('Da\User\Helper\AuthHelper');

        // email change strategy
        $di->set('Da\User\Strategy\DefaultEmailChangeStrategy');
        $di->set('Da\User\Strategy\InsecureEmailChangeStrategy');
        $di->set('Da\User\Strategy\SecureEmailChangeStrategy');

        // active query classes
        Yii::$container->set('Da\User\Query\AccountQuery');
        Yii::$container->set('Da\User\Query\ProfileQuery');
        Yii::$container->set('Da\User\Query\TokenQuery');
        Yii::$container->set('Da\User\Query\UserQuery');
    }
}
