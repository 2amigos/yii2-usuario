<?php

/*
 * This file is part of the 2amigos/yii2-usuario project.
 *
 * (c) 2amigOS! <http://2amigos.us/>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Da\User\Service;

use Da\User\Contracts\MailChangeStrategyInterface;
use Da\User\Contracts\ServiceInterface;
use Da\User\Model\Token;
use Da\User\Model\User;
use Da\User\Query\TokenQuery;
use Da\User\Query\UserQuery;
use Da\User\Traits\ModuleAwareTrait;
use Yii;

class EmailChangeService implements ServiceInterface
{
    use ModuleAwareTrait;

    protected $code;
    protected $model;
    protected $tokenQuery;
    protected $userQuery;

    public function __construct($code, User $model, TokenQuery $tokenQuery, UserQuery $userQuery)
    {
        $this->code = $code;
        $this->model = $model;
        $this->tokenQuery = $tokenQuery;
        $this->userQuery = $userQuery;
    }

    public function run()
    {
        /** @var Token $token */
        $token = $this->tokenQuery
            ->whereUserId($this->model->id)
            ->whereCode($this->code)
            ->whereIsTypes([Token::TYPE_CONFIRM_NEW_EMAIL, Token::TYPE_CONFIRM_OLD_EMAIL])
            ->one();

        if ($token === null || $token->getIsExpired()) {
            Yii::$app->session->setFlash('danger', Yii::t('usuario', 'Your confirmation token is invalid or expired'));

            return false;
        }
        $token->delete();
        if (empty($this->model->unconfirmed_email)) {
            Yii::$app->session->setFlash('danger', Yii::t('usuario', 'An error occurred processing your request'));
        } elseif ($this->userQuery->whereEmail($this->model->unconfirmed_email)->exists() === false) {
            if ($this->getModule()->emailChangeStrategy === MailChangeStrategyInterface::TYPE_SECURE) {
                if ($token->type === Token::TYPE_CONFIRM_NEW_EMAIL) {
                    $this->model->flags |= User::NEW_EMAIL_CONFIRMED;
                    Yii::$app->session->setFlash(
                        'success',
                        Yii::t(
                            'usuario',
                            'Awesome, almost there. Now you need to click the confirmation link sent to your old email address.'
                        )
                    );
                } elseif ($token->type === Token::TYPE_CONFIRM_OLD_EMAIL) {
                    $this->model->flags |= User::OLD_EMAIL_CONFIRMED;
                    Yii::$app->session->setFlash(
                        'success',
                        Yii::t(
                            'usuario',
                            'Awesome, almost there. Now you need to click the confirmation link sent to your new email address.'
                        )
                    );
                }
            }
            if ((($this->model->flags & User::NEW_EMAIL_CONFIRMED) && ($this->model->flags & User::OLD_EMAIL_CONFIRMED))
                || $this->getModule()->emailChangeStrategy === MailChangeStrategyInterface::TYPE_DEFAULT
            ) {
                $this->model->email = $this->model->unconfirmed_email;
                $this->model->unconfirmed_email = null;
                Yii::$app->session->setFlash('success', Yii::t('usuario', 'Your email address has been changed'));
            }

            return $this->model->save(false);
        }

        return false;
    }
}
