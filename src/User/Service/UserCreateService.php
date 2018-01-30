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

use Da\User\Contracts\ServiceInterface;
use Da\User\Event\UserEvent;
use Da\User\Helper\SecurityHelper;
use Da\User\Model\User;
use Da\User\Traits\MailAwareTrait;
use Exception;
use Yii;
use yii\base\InvalidCallException;

class UserCreateService implements ServiceInterface
{
    use MailAwareTrait;

    protected $model;
    protected $securityHelper;
    protected $mailService;

    public function __construct(User $model, MailService $mailService, SecurityHelper $securityHelper)
    {
        $this->model = $model;
        $this->mailService = $mailService;
        $this->securityHelper = $securityHelper;
    }

    /**
     * @throws InvalidCallException
     * @throws \yii\db\Exception
     * @return bool
     *
     */
    public function run()
    {
        $model = $this->model;

        if ($model->getIsNewRecord() === false) {
            throw new InvalidCallException('Cannot create a new user from an existing one.');
        }

        $transaction = $model::getDb()->beginTransaction();

        try {
            $model->confirmed_at = time();
            $model->password = !empty($model->password)
                ? $model->password
                : $this->securityHelper->generatePassword(8);

            $model->trigger(UserEvent::EVENT_BEFORE_CREATE);

            if (!$model->save()) {
                $transaction->rollBack();
                return false;
            }

            $model->trigger(UserEvent::EVENT_AFTER_CREATE);
            if (!$this->sendMail($model)) {
                Yii::$app->session->setFlash(
                    'warning',
                    Yii::t(
                        'usuario',
                        'Error sending welcome message to "{email}". Please try again later.',
                        ['email' => $model->email]
                    )
                );
                $transaction->rollBack();
                return false;
            }
            $transaction->commit();
            return true;
        } catch (Exception $e) {
            $transaction->rollBack();
            Yii::error($e->getMessage(), 'usuario');

            return false;
        }
    }
}
