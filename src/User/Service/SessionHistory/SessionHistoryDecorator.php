<?php

/*
 * This file is part of the 2amigos/yii2-usuario project.
 *
 * (c) 2amigOS! <http://2amigos.us/>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Da\User\Service\SessionHistory;

use Da\User\Model\SessionHistory;
use Da\User\Query\SessionHistoryCondition;
use Da\User\Query\SessionHistoryQuery;
use Da\User\Traits\ModuleAwareTrait;
use Yii;
use yii\db\Exception;
use yii\web\Session;
use yii\base\InvalidArgumentException as BaseInvalidArgumentException;

/**
 * Decorator for the {@see Session} class for storing the 'session history'
 *
 * Not decorated methods:
 * {@see Session::open()}
 * {@see Session::close()}
 * {@see Session::destroy()}
 * {@see Session::get()}
 * {@see Session::set()}
 */
class SessionHistoryDecorator extends Session
{
    use ModuleAwareTrait;

    public $sessionHistoryTable = '{{%session_history}}';

    /**
     * @var Session
     */
    public $session;

    public $condition;

    public function __construct(
        Session $session,
        SessionHistoryCondition $historyCondition,
        $config = []
    ) {
        $this->session = $session;
        $this->condition = $historyCondition;

        parent::__construct($config);
    }

    /** @inheritdoc */
    public function getUseCustomStorage()
    {
        return $this->session->getUseCustomStorage();
    }

    /** @inheritdoc */
    public function getIsActive()
    {
        return $this->session->getIsActive();
    }

    /** @inheritdoc */
    public function getHasSessionId()
    {
        return $this->session->getHasSessionId();
    }

    /** @inheritdoc */
    public function setHasSessionId($value)
    {
        return $this->session->setHasSessionId($value);
    }

    /** @inheritdoc */
    public function getId()
    {
        return $this->session->getId();
    }

    /** @inheritdoc */
    public function setId($value)
    {
        return $this->session->setId($value);
    }

    /** @inheritdoc */
    public function regenerateID($deleteOldSession = false)
    {
        return $this->getDb()->transaction(function () use ($deleteOldSession) {
            $oldSid = session_id();
            if (false === $this->session->regenerateID($deleteOldSession)) {
                return false;
            }

            if (false === $this->getModule()->enableSessionHistory) {
                return true;
            }

            $user = Yii::$app->user;
            if ($user->getIsGuest()) {
                $this->unbindSessionHistory($oldSid);
            } else {
                $this->getDB()->createCommand()
                    ->delete(
                        $this->sessionHistoryTable,
                        $this->condition->byUserSession($user->getId(), $oldSid)
                    )->execute();
            }

            return true;
        });
    }

    /** @inheritdoc */
    public function getName()
    {
        return $this->session->getName();
    }

    /** @inheritdoc */
    public function setName($value)
    {
        return $this->session->setName($value);
    }

    /** @inheritdoc */
    public function getSavePath()
    {
        return $this->session->getSavePath();
    }

    /** @inheritdoc */
    public function setSavePath($value)
    {
        return $this->session->setSavePath($value);
    }

    /** @inheritdoc */
    public function getCookieParams()
    {
        return $this->session->getCookieParams();
    }

    /** @inheritdoc */
    public function setCookieParams(array $value)
    {
        return $this->session->setCookieParams($value);
    }

    /** @inheritdoc */
    public function getUseCookies()
    {
        return $this->session->getUseCookies();
    }

    /** @inheritdoc */
    public function setUseCookies($value)
    {
        return $this->session->setUseCookies($value);
    }

    /** @inheritdoc */
    public function getGCProbability()
    {
        return $this->session->getGCProbability();
    }

    /** @inheritdoc */
    public function setGCProbability($value)
    {
        return $this->session->setGCProbability($value);
    }

    /** @inheritdoc */
    public function getUseTransparentSessionID()
    {
        return $this->session->getUseTransparentSessionID();
    }

    /** @inheritdoc */
    public function setUseTransparentSessionID($value)
    {
        return $this->session->setUseTransparentSessionID($value);
    }

    /** @inheritdoc */
    public function getTimeout()
    {
        return $this->session->getTimeout();
    }

    /** @inheritdoc */
    public function setTimeout($value)
    {
        return $this->session->setTimeout($value);
    }

    /** @inheritdoc */
    public function openSession($savePath, $sessionName)
    {
        return $this->session->openSession($savePath, $sessionName);
    }

    /** @inheritdoc */
    public function closeSession()
    {
        return $this->session->closeSession();
    }

    /** @inheritdoc */
    public function readSession($id)
    {
        return $this->session->readSession($id);
    }

    /** @inheritdoc */
    public function writeSession($id, $data)
    {
        return $this->session->writeSession($id, $data) &&
            (
                false === $this->getModule()->enableSessionHistory ||
                $this->getDb()->transaction(function () use ($id, $data) {
                    if (Yii::$app->user->getIsGuest()) {
                        return true;
                    }

                    $updatedAt = ['updated_at' => time()];

                    $model = $this->getHistoryQuery()
                        ->whereCurrentUser()
                        ->one();
                    if (isset($model)) {
                        $model->updateAttributes($updatedAt);
                        $result = true;
                    } else {
                        $model = Yii::createObject([
                                'class' => SessionHistory::class,
                            ] + $this->condition->currentUserData() + $updatedAt);
                        if (!$result = $model->save()) {
                            throw new BaseInvalidArgumentException(
                                print_r($model->errors, 1)
                            );
                        }

                        $this->displacementHistory($model->user_id);
                    }

                    return $result;
                })
            );

    }

    /** @inheritdoc */
    public function destroySession($id)
    {
        return $this->session->destroySession($id) &&
            (
                false === $this->getModule()->enableSessionHistory ||
                $this->getDb()->transaction(function () use ($id) {
                    $this->unbindSessionHistory($id);

                    return true;
                })
            );
    }

    /** @inheritdoc */
    public function gcSession($maxLifetime)
    {
        return $this->session->gcSession($maxLifetime) &&
            (
                false === $this->getModule()->enableSessionHistory ||
                $this->getDb()->transaction(function () use ($maxLifetime) {
                    $this->getDb()->createCommand()->update(
                        $this->sessionHistoryTable,
                        $this->condition->inactiveData(),
                        $this->condition->expired()
                    )->execute();
                    return true;
                })
            );
    }

    /** @inheritdoc */
    public function getIterator()
    {
        return $this->session->getIterator();
    }

    /** @inheritdoc */
    public function getCount()
    {
        return $this->session->getCount();
    }

    /** @inheritdoc */
    public function count()
    {
        return $this->session->count();
    }

    /** @inheritdoc */
    public function remove($key)
    {
        return $this->session->remove($key);
    }

    /** @inheritdoc */
    public function removeAll()
    {
        return $this->session->removeAll();
    }

    /** @inheritdoc */
    public function has($key)
    {
        return $this->session->has($key);
    }

    /** @inheritdoc */
    public function getFlash($key, $defaultValue = null, $delete = false)
    {
        return $this->session->getFlash($key, $defaultValue, $delete);
    }

    /** @inheritdoc */
    public function getAllFlashes($delete = false)
    {
        return $this->session->getAllFlashes($delete);
    }

    /** @inheritdoc */
    public function setFlash($key, $value = true, $removeAfterAccess = true)
    {
        return $this->session->setFlash($key, $value, $removeAfterAccess);
    }

    /** @inheritdoc */
    public function addFlash($key, $value = true, $removeAfterAccess = true)
    {
        return $this->session->addFlash($key, $value, $removeAfterAccess);
    }

    /** @inheritdoc */
    public function removeFlash($key)
    {
        return $this->session->removeFlash($key);
    }

    /** @inheritdoc */
    public function removeAllFlashes()
    {
        return $this->session->removeAllFlashes();
    }

    /** @inheritdoc */
    public function hasFlash($key)
    {
        return $this->session->hasFlash($key);
    }

    /** @inheritdoc */
    public function offsetExists($offset)
    {
        return $this->session->offsetExists($offset);
    }

    /** @inheritdoc */
    public function offsetGet($offset)
    {
        return $this->session->offsetGet($offset);
    }

    /** @inheritdoc */
    public function offsetSet($offset, $item)
    {
        return $this->session->offsetSet($offset, $item);
    }

    /** @inheritdoc */
    public function offsetUnset($offset)
    {
        return $this->session->offsetUnset($offset);
    }

    /** @inheritdoc */
    public function setCacheLimiter($cacheLimiter)
    {
        return $this->session->setCacheLimiter($cacheLimiter);
    }

    /** @inheritdoc */
    public function getCacheLimiter()
    {
        return $this->session->getCacheLimiter();
    }

    /**
     * @param string $id
     * @return bool
     * @throws Exception
     */
    protected function unbindSessionHistory($id)
    {
        return (bool)$this->getDb()->createCommand()->update(
            $this->sessionHistoryTable,
            $this->condition->unbindSession(),
            $this->condition->bySession($id)
        )->execute();
    }

    /**
     *
     * @param int $userId
     * @return bool
     * @throws Exception
     */
    protected function displacementHistory($userId)
    {
        $module = $this->getModule();

        if (false === $module->hasNumberSessionHistory()) {
            return true;
        }

        $updatedAt = $this->getHistoryQuery()
            ->oldestUpdatedTimeActiveSession($userId);

        if (!$updatedAt) {
            return true;
        }

        $this->getDB()->createCommand()->delete(
            $this->sessionHistoryTable,
            $this->condition->shouldDeleteBefore(intval($updatedAt), $userId)
        )->execute();

        return true;
    }

    /**
     * @return SessionHistoryQuery
     */
    protected function getHistoryQuery()
    {
        return Yii::$container->get(SessionHistoryQuery::class);
    }

    protected function getDb()
    {
        return Yii::$app->getDb();
    }
}