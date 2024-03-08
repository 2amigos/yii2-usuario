<?php

/*
 * This file is part of the 2amigos/yii2-usuario project.
 *
 * (c) 2amigOS! <http://2amigos.us/>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Da\User\Controller;

use Da\User\Model\User;
use Da\User\Query\ProfileQuery;
use Da\User\Traits\ModuleAwareTrait;
use Yii;
use yii\base\Module;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;

class ProfileController extends Controller
{
    use ModuleAwareTrait;

    /** @var int will allow only profile owner */
    const PROFILE_VISIBILITY_OWNER = 0;
    /** @var int will allow profile owner and admin users */
    const PROFILE_VISIBILITY_ADMIN = 1;
    /** @var int will allow any logged-in user */
    const PROFILE_VISIBILITY_USERS = 2;
    /** @var int will allow anyone, including gusets */
    public const PROFILE_VISIBILITY_PUBLIC = 3;

    protected $profileQuery;

    /**
     * ProfileController constructor.
     *
     * @param string       $id
     * @param Module       $module
     * @param ProfileQuery $profileQuery
     * @param array        $config
     */
    public function __construct($id, Module $module, ProfileQuery $profileQuery, array $config = [])
    {
        $this->profileQuery = $profileQuery;
        parent::__construct($id, $module, $config);
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index'],
                        'roles' => ['@'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['show'],
                        'roles' => ['?', '@'],
                    ],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        return $this->redirect(['show', 'id' => Yii::$app->user->getId()]);
    }

    public function actionShow($id)
    {
        $user = Yii::$app->user;
        $id = (int) $id;

        /** @var ?User $identity */
        $identity = $user->getIdentity();

        switch($this->module->profileVisibility) {
            case static::PROFILE_VISIBILITY_OWNER:
                if($identity === null || $id !== $user->getId()) {
                    throw new ForbiddenHttpException("1");
                }
                break;
            case static::PROFILE_VISIBILITY_ADMIN:
                if($id === $user->getId() || ($identity !== null && $identity->getIsAdmin())) {
                    break;
                }
                throw new ForbiddenHttpException();
            case static::PROFILE_VISIBILITY_USERS:
                if((!$user->getIsGuest())) {
                    break;
                }
                throw new ForbiddenHttpException();
            case static::PROFILE_VISIBILITY_PUBLIC:
                break;
            default:
                throw new ForbiddenHttpException();

        }

        $profile = $this->profileQuery->whereUserId($id)->one();

        if ($profile === null) {
            throw new NotFoundHttpException();
        }

        return $this->render(
            'show',
            [
                'profile' => $profile,
            ]
        );
    }
}
