<?php
namespace Da\User\Widget;


use Yii;
use yii\authclient\ClientInterface;
use yii\authclient\widgets\AuthChoice;
use yii\authclient\widgets\AuthChoiceAsset;
use yii\helpers\Html;
use yii\helpers\Url;

class ConnectWidget extends AuthChoice
{
    /**
     * @var array|null An array of user's accounts
     */
    public $accounts;

    /**
     * @inheritdoc
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
     * @inheritdoc
     */
    public function createClientUrl($provider)
    {
        if ($this->isConnected($provider)) {
            return Url::to(['/user/settings/disconnect', 'id' => $this->accounts[$provider->getId()]->id]);
        } else {
            return parent::createClientUrl($provider);
        }
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
        return $this->accounts != null && isset($this->accounts[$provider->getId()]);
    }
}
