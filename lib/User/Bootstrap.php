<?php

namespace Da\User;

use Da\User\Helper\ClassMapHelper;
use Yii;
use yii\authclient\Collection;
use yii\base\Application;
use yii\base\BootstrapInterface;
use yii\base\Exception;
use yii\console\Application as ConsoleApplication;
use yii\i18n\PhpMessageSource;
use yii\web\Application as WebApplication;

class Bootstrap implements BootstrapInterface
{
    /**
     * @inheritdoc
     */
    public function bootstrap($app)
    {
        if ($app->hasModule('user') && $app->getModule('user') instanceof Module) {
            $classMap = $this->buildClassMap();
            $this->initContainer($classMap);
            $this->initTranslations($app);

            if ($app instanceof WebApplication) {
                $this->initUrlRoutes($app);
                $this->initAuthCollection($app);
            } else {
                /** @var $app ConsoleApplication */
                $this->initConsoleCommands($app);
            }
        }
    }

    /**
     * Initialize container with module classes
     *
     * @param array $map the previously built class map list
     */
    protected function initContainer($map)
    {
        $di = Yii::$container;

        // email change strategy
        $di->set('Da\User\Strategy\DefaultEmailChangeStrategy');
        $di->set('Da\User\Strategy\InsecureEmailChangeStrategy');
        $di->set('Da\User\Strategy\SecureEmailChangeStrategy');

        // models + active query classes
        $modelClassMap = [];
        foreach ($map as $class => $definition) {

            $di->set($class, $definition);
            $model = is_array($definition) ? $definition['class'] : $definition;
            $name = (substr($class, strrpos($class, '\\') + 1));
            $modelClassMap[$name] = $model;

            if (in_array($name, ['User', 'Profile', 'Token', 'Account'])) {
                $di->set(
                    $name . 'Query',
                    function () use ($model) {
                        return $model->find();
                    }
                );
            }
        }

        // helpers
        $di->set('Da\User\Helper\AuthHelper');
        $di->setSingleton(ClassMapHelper::class, ClassMapHelper::class, [$modelClassMap]);

        if (php_sapi_name() !== 'cli') {
            // override Yii
            $di->set(
                'yii\web\User',
                [
                    'enableAutoLogin' => true,
                    'loginUrl' => ['/user/auth/login'],
                    'identityClass' => $di->get(ClassMapHelper::class)->get('User')
                ]
            );
        }
    }

    /**
     * Registers module translation messages
     *
     * @param Application $app
     */
    protected function initTranslations(Application $app)
    {
        if (!isset($app->get('i18n')->translations['user*'])) {
            $app->get('i18n')->translations['user*'] = [
                'class' => PhpMessageSource::class,
                'basePath' => __DIR__ . '/../i18n',
                'sourceLanguage' => 'en-US'
            ];
        }
    }

    /**
     * Initializes web url routes (rules in Yii2)
     *
     * @param WebApplication $app
     */
    protected function initUrlRoutes(WebApplication $app)
    {
        /** @var $module Module */
        $module = $app->getModule('user');
        $config = [
            'class' => 'yii\web\GroupUrlRule',
            'prefix' => $module->prefix,
            'rules' => $module->routes,
        ];

        if ($module->prefix !== 'user') {
            $config['routePrefix'] = 'user';
        }

        $rule = Yii::createObject($config);
        $app->getUrlManager()->addRules([$rule], false);
    }

    /**
     * Ensures the authCollection component is configured.
     *
     * @param WebApplication $app
     */
    protected function initAuthCollection(WebApplication $app)
    {
        if (!$app->has('authClientCollection')) {
            $app->set('authClientCollection', Collection::class);
        }
    }

    /**
     * Registers console commands to main app.
     *
     * @param ConsoleApplication $app
     */
    protected function initConsoleCommands(ConsoleApplication $app)
    {
        $app->getModule('user')->controllerNamespace = 'Da\User\Command';
    }

    /**
     * Builds class map according to use configuration
     *
     * @return array
     */
    protected function buildClassMap()
    {
        $map = [];

        $defaults = [
            'User' => 'Da\User\Model\User',
            'Account' => 'Da\User\Model\Account',
            'Profile' => 'Da\User\Model\Profile',
            'Token' => 'Da\User\Model\Token',
            // ---
            'UserSearch' => 'Da\User\Search\UserSearch',
            // ---
            'RegistrationForm' => 'Da\User\Form\RegistrationForm',
            'ResendForm' => 'Da\User\Form\ResendForm',
            'LoginForm' => 'Da\User\Form\LoginForm',
            'SettingsForm' => 'Da\User\Form\SettingsForm',
            'RecoveryForm' => 'Da\User\Form\RecoveryForm',
        ];

        $routes = [
            'Da\User\Model' => [
                'User',
                'Account',
                'Profile',
                'Token'
            ],
            'Da\User\Search' => [
                'UserSearch'
            ],
            'Da\UserForm' => [
                'RegistrationForm',
                'ResendForm',
                'LoginForm',
                'SettingsForm',
                'RecoveryForm',
            ]
        ];

        foreach ($defaults as $name => $definition) {
            $map[$this->getRoute($routes, $name) . "\\$name"] = $definition;
        }

        return $map;
    }

    /**
     * Returns the parent class name route of a short class name
     *
     * @param array $routes class name routes
     * @param string $name
     *
     * @return int|string
     * @throws Exception
     */
    protected function getRoute(array $routes, $name)
    {
        foreach ($routes as $route => $names) {
            if (in_array($name, $names)) {
                return $route;
            }
        }
        throw new Exception('Unknown configuration class name');
    }

}
