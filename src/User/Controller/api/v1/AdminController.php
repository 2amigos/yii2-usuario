<?php

namespace Da\User\Controller\api\v1;

use Da\User\Event\UserEvent;
use Da\User\Factory\MailFactory;
use Da\User\Model\Assignment;
use Da\User\Model\Profile;
use Da\User\Model\User;
use Da\User\Query\UserQuery;
use Da\User\Service\PasswordExpireService;
use Da\User\Service\PasswordRecoveryService;
use Da\User\Service\UserBlockService;
use Da\User\Service\UserConfirmationService;
use Da\User\Service\UserCreateService;
use Da\User\Traits\ContainerAwareTrait;
use Yii;
use yii\base\Module;
use yii\db\ActiveRecord;
use yii\filters\Cors;
use yii\rest\ActiveController;
use yii\web\BadRequestHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\ServerErrorHttpException;

/**
 * Controller that provides REST APIs to manage users.
 * This controller is equivalent to `Da\User\Controller\AdminController`.
 * 
 * TODO: 
 * - `Info` and `SwitchIdentity` actions were not developed yet.
 * - `Assignments` action implements only GET method (POST method not developed yet).
 */
class AdminController extends ActiveController
{
    use ContainerAwareTrait;

    /**
     * {@inheritdoc}
     */
    public $modelClass = 'Da\User\Model\User';

    /**
     * {@inheritdoc}
     */
    public $updateScenario = 'update';
    
    /**
     * {@inheritdoc}
     */
    public $createScenario = 'create';

    /**
     * @var UserQuery
     */
    protected $userQuery;

    /**
     * AdminController constructor.
     * @param string $id
     * @param Module $module
     * @param UserQuery $userQuery
     * @param array $config
     */
    public function __construct($id, Module $module, UserQuery $userQuery, array $config = [])
    {
        $this->userQuery = $userQuery;
        parent::__construct($id, $module, $config);
    }

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();
        // Set user properties for REST APIs
        \Yii::$app->user->enableSession = false;
        \Yii::$app->user->loginUrl = null;
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        // Get and then remove some default actions
        $actions = parent::actions();
        unset($actions['create']);
        unset($actions['update']);
        unset($actions['delete']);
        return $actions;
    }

    /**
     * {@inheritdoc}
     */
    protected function verbs()
    {
        // Get parent verbs
        $verbs = parent::verbs();

        // Add new verbs and return
        $verbs['update-profile'] = ['PUT', 'PATCH'];
        $verbs['assignments'] = ['GET'];
        $verbs['confirm'] = ['PUT', 'PATCH'];
        $verbs['block'] = ['PUT', 'PATCH'];
        $verbs['password-reset'] = ['PUT', 'PATCH'];
        $verbs['force-password-change'] = ['PUT', 'PATCH'];
        return $verbs;
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        // Remove the (default) authentication filter
        unset($behaviors['authenticator']);

        // Cors filter
        $behaviors['corsFilter'] = [
            'class' => Cors::class,
        ];

        // Re-add authentication filter
        $behaviors['authenticator'] = [
            'class' => $this->module->authenticatorClass, // Class depends on the module parameter
            'except' => ['options']
        ];
        // Return
        return $behaviors;
    }

    /**
     * {@inheritdoc}
     */
    public function checkAccess($action, $model = null, $params = [])
    {
        // Access for admins only
        if (!Yii::$app->user->can('admin')) {
            throw new ForbiddenHttpException(Yii::t('usuario', 'User does not have sufficient permissions.'));
        }
    }

    /**
     * Create a user.
     */
    public function actionCreate()
    {
        // Check access
        $this->checkAccess($this->action);

        // Create new user model
        /** @var User $user */
        $user = $this->make(User::class, [], ['scenario' => $this->createScenario]);

        // Create event object
        /** @var UserEvent $event */
        $event = $this->make(UserEvent::class, [$user]);

        // Save user model + response
        $user->load(Yii::$app->getRequest()->getBodyParams(), '');
        if ($user->validate()) {
            $this->trigger(UserEvent::EVENT_BEFORE_CREATE, $event);
            $mailService = MailFactory::makeWelcomeMailerService($user); // Welcome email
            if ($this->make(UserCreateService::class, [$user, $mailService])->run()) {
                $this->trigger(UserEvent::EVENT_AFTER_CREATE, $event);
                Yii::$app->getResponse()->setStatusCode(201); // 201 = Created
                return $user;
            }
        }
        if (!$user->hasErrors()) {
            $this->throwServerError();
        }
        return $user;
    }

    /**
     * Update a user.
     * @param int $id ID of the user.
     */
    public function actionUpdate($id)
    {
        // Check access
        $this->checkAccess($this->action);

        // Get user model
        /** @var User $user */
        $user = $this->userQuery->where(['id' => $id])->one();
        if (empty($user)) { // Check user, so `$id` parameter
            $this->throwUser404();
        }
        $user->setScenario($this->updateScenario);

        // Create event object
        /** @var UserEvent $event */
        $event = $this->make(UserEvent::class, [$user]);

        // Save user model + response
        $user->load(Yii::$app->getRequest()->getBodyParams(), '');
        if ($user->validate()) {
            $this->trigger(UserEvent::EVENT_BEFORE_ACCOUNT_UPDATE, $event);
            if ($user->save()) {
                $this->trigger(UserEvent::EVENT_AFTER_ACCOUNT_UPDATE, $event);
                return $user;
            }
        }
        if (!$user->hasErrors()) {
            $this->throwServerError();
        }
        return $user;
    }

    /**
     * Delete a user.
     * @param int $id ID of the user.
     */
    public function actionDelete($id)
    {
        // Check access
        $this->checkAccess($this->action);

        // Check ID parameter (whether own account)
        if ((int)$id === Yii::$app->user->getId()) {
            throw new BadRequestHttpException(Yii::t('usuario', 'You cannot remove your own account.'));
        }

        // Get user model
        /** @var User $user */
        $user = $this->userQuery->where(['id' => $id])->one();
        if (empty($user)) { // Check user, so `$id` parameter
            $this->throwUser404();
        }
        
        // Create event object
        /** @var UserEvent $event */
        $event = $this->make(UserEvent::class, [$user]);

        // Detele user model + response
        $this->trigger(ActiveRecord::EVENT_BEFORE_DELETE, $event);
        if ($user->delete()) {
            $this->trigger(ActiveRecord::EVENT_AFTER_DELETE, $event);
            Yii::$app->getResponse()->setStatusCode(204); // 204 = No Content
        }
        else {
            $this->throwServerError();
        }
    }

    /**
     * Update the user profile.
     * @param int $id ID of the user.
     */
    public function actionUpdateProfile($id)
    {
        // Check access
        $this->checkAccess($this->action);

        // Get user model
        /** @var User $user */
        $user = $this->userQuery->where(['id' => $id])->one();
        if (empty($user)) { // Check user, so `$id` parameter
            $this->throwUser404();
        }

        // Get profile model
        /** @var Profile $profile */
        $profile = $user->profile;
        if ($profile === null) {
            $profile = $this->make(Profile::class);
            $profile->link('user', $user);
        }

        // Create event object
        /** @var UserEvent $event */
        $event = $this->make(UserEvent::class, [$user]);

        // Save profile model + response
        $profile->load(Yii::$app->getRequest()->getBodyParams(), '');
        $this->trigger(UserEvent::EVENT_BEFORE_PROFILE_UPDATE, $event);
        if ($profile->save() === false && !$profile->hasErrors()) {
            $this->throwServerError();
        }
        $this->trigger(UserEvent::EVENT_AFTER_PROFILE_UPDATE, $event);
        return $profile;
    }

    /**
     * Get assignments of the specified user.
     * @param int $id ID of the user.
     */
    public function actionAssignments($id)
    {
        // Check access
        $this->checkAccess($this->action);

        // Get user model
        /** @var User $user */
        $user = $this->userQuery->where(['id' => $id])->one();
        if (empty($user)) { // Check user, so `$id` parameter
            $this->throwUser404();
        }

        // Get assignments + response
        $assignments = $this->make(Assignment::class, [], ['user_id' => $user->id]);
        return $assignments;
    }

    /**
     * Confirm the user.
     * @param int $id ID of the user.
     */
    public function actionConfirm($id)
    {
        // Check access
        $this->checkAccess($this->action);

        // Get user model
        /** @var User $user */
        $user = $this->userQuery->where(['id' => $id])->one();
        if (empty($user)) { // Check user, so `$id` parameter
            $this->throwUser404();
        }

        // Create event object
        /** @var UserEvent $event */
        $event = $this->make(UserEvent::class, [$user]);

        // Confirm user + response
        $this->trigger(UserEvent::EVENT_BEFORE_CONFIRMATION, $event);
        if ($this->make(UserConfirmationService::class, [$user])->run() || $user->hasErrors()) {
            $this->trigger(UserEvent::EVENT_AFTER_CONFIRMATION, $event);
            return $user;
        }
        else {
            $this->throwServerError();
        }
    }

    /**
     * Block and unblock the user.
     * @param int $id ID of the user.
     */
    public function actionBlock($id)
    {
        // Check access
        $this->checkAccess($this->action);

        // Check ID parameter (whether own account)
        if ((int)$id === Yii::$app->user->getId()) {
            throw new BadRequestHttpException(Yii::t('usuario', 'You cannot block your own account.'));
        }

        // Get user model
        /** @var User $user */
        $user = $this->userQuery->where(['id' => $id])->one();
        if (empty($user)) { // Check user, so `$id` parameter
            $this->throwUser404();
        }

        // Create event object
        /** @var UserEvent $event */
        $event = $this->make(UserEvent::class, [$user]);

        // Block user + response
        if ($this->make(UserBlockService::class, [$user, $event, $this])->run() || $user->hasErrors()) {
            return $user;
        }
        else {
            $this->throwServerError();
        }
    }

    /**
     * Reset password.
     * @param int $id ID of the user.
     */
    public function actionPasswordReset($id)
    {
        // Check access
        $this->checkAccess($this->action);

        // Get user model
        /** @var User $user */
        $user = $this->userQuery->where(['id' => $id])->one();
        if (empty($user)) { // Check user, so `$id` parameter
            $this->throwUser404();
        }

        // Confirm user + response
        $mailService = MailFactory::makeRecoveryMailerService($user->email);
        if ($this->make(PasswordRecoveryService::class, [$user->email, $mailService])->run()) {
            return $user;
        }
        else {
            $this->throwServerError();
        }
    }
    
    /**
     * Forces the user to change password at next login.
     * @param int $id ID of the user.
     */
    public function actionForcePasswordChange($id)
    {
        // Check access
        $this->checkAccess($this->action);

        // Get user model
        /** @var User $user */
        $user = $this->userQuery->where(['id' => $id])->one();
        if (empty($user)) { // Check user, so `$id` parameter
            $this->throwUser404();
        }

        // Confirm user + response
        if ($this->make(PasswordExpireService::class, [$user])->run()) {
            return $user;
        }
        else {
            $this->throwServerError();
        }
    }

    /**
     * Handle server error (with default Yii2 response).
     * @return void
     * @throws ServerErrorHttpException
     */
    protected function throwServerError()
    {
        throw new ServerErrorHttpException('Failed to create the object for unknown reason.'); // Yii2 standard response for server errors
    }

    /**
     * Handle 404 error for user (usually if the entered ID is not valid).
     * @return void
     * @throws NotFoundHttpException
     */
    protected function throwUser404()
    {
        throw new NotFoundHttpException(Yii::t('usuario', 'User not found.'));
    }



}