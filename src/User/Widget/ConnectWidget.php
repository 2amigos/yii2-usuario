<?php

/*
 * This file is part of the 2amigos/yii2-usuario project.
 *
 * (c) 2amigOS! <http://2amigos.us/>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Da\User\Widget;

use Yii;
use yii\authclient\ClientInterface;
use yii\authclient\widgets\AuthChoice;
use yii\authclient\widgets\AuthChoiceAsset;
use yii\base\InvalidParamException;
use yii\helpers\Html;
use yii\helpers\Url;

class ConnectWidget extends AuthChoice
{
    /**
     * @var array|null An array of user's accounts
     */
    public $accounts;

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        AuthChoiceAsset::register(Yii::$app->view);
        if ($this->popupMode) {
            Yii::$app->view->registerJs("\$('#" . $this->getId() . "').authchoice();");
        }
        $this->options['id'] = $this->getId();
        echo Html::beginTag('div', $this->options);
    }

    /**
     * {@inheritdoc}
     *
     * @throws InvalidParamException
     */
    public function createClientUrl($provider)
    {
        if ($this->isConnected($provider)) {
            return Url::to(['/user/settings/disconnect', 'id' => $this->accounts[$provider->getId()]->id]);
        }

        return parent::createClientUrl($provider);
    }

    /**
     * Checks if provider already connected to user.
     *
     * @param ClientInterface $provider
     *
     * @return bool
     */
    public function isConnected(ClientInterface $provider)
    {
        return null !== $this->accounts && isset($this->accounts[$provider->getId()]);
    }
}
