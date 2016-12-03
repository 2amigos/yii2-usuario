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
        // helpers
        Yii::$container->set(AuthHelper::class);

        // active query classes
        Yii::$container->set(AccountQuery::class);
        Yii::$container->set(ProfileQuery::class);
        Yii::$container->set(TokenQuery::class);
        Yii::$container->set(UserQuery::class);
    }
}
