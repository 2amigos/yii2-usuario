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
use yii\base\InvalidConfigException;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\InputWidget;

class ReCaptchaWidget extends InputWidget
{
    /**
     * @var string the color theme of the widget. Values can be 'dark' or 'light'. Optional.
     */
    public $theme;
    /**
     * @var string the type of captcha. Values can be 'audio' or 'image'. Optional.
     */
    public $type;
    /**
     * @var string the size of the widget. Values can be 'compact' or 'normal'. Optional.
     */
    public $size;
    /**
     * @var string the tabindex of the widget and challenge. Optional.
     */
    public $tabIndex;
    /**
     * @var string the name of the callbaqck function to be executed when the user submits a
     *             successful CAPTCHA response. The user's response, *g-recaptcha-response*, will be the input
     *             for the callback function. Optional.
     */
    public $callback;
    /**
     * @var string the name of the callback function to be executed when the recaptcha response
     *             expires and the user needs to solve a new CAPTCHA.
     */
    public $expiredCallback;

    /**
     * @inheritdoc
     *
     * @throws InvalidConfigException
     */
    public function init()
    {
        if (!Yii::$app->get('recaptcha')) {
            throw new InvalidConfigException(Yii::t('usuario', 'The "recaptcha" component must be configured.'));
        }

        parent::init();

        $this->registerClientScript();
    }

    /**
     * @inheritdoc
     *
     * @throws InvalidConfigException
     */
    public function run()
    {
        $html = [];

        $html[] = $this->hasModel()
            ? Html::activeHiddenInput($this->model, $this->attribute, $this->options)
            : Html::hiddenInput($this->name, null, $this->options);

        $html[] = Html::tag('div', '', $this->getCaptchaOptions());

        return implode("\n", $html);
    }

    /**
     * @throws InvalidConfigException
     * @return array                  the google recaptcha options.
     */
    protected function getCaptchaOptions()
    {
        $data = [
            'sitekey' => Yii::$app->get('recaptcha')->key,
            'callback' => $this->callback,
            'expired-callback' => $this->expiredCallback,
            'theme' => $this->theme,
            'type' => $this->type,
            'size' => $this->size,
            'tabindex' => $this->tabIndex
        ];

        $options = [
            'class' => 'g-recaptcha',
            'data' => array_filter($data)
        ];

        return $options;
    }

    /**
     * Registers the required libraries and scripts for the widget to work.
     */
    protected function registerClientScript()
    {
        $view = $this->getView();

        $view->registerJsFile(
            '//www.google.com/recaptcha/api.js?hl=' . $this->getLanguageCode(),
            [
                'position' => View::POS_HEAD,
                'async' => true,
                'defer' => true
            ]
        );

        $js = [];
        $id = $this->options['id'];

        $js[] = empty($this->callback)
            ? "var reCaptchaCallback = function(r){jQuery('#{$id}').val(r);};"
            : "var reCaptchaCallback = function(r){jQuery('#{$id}').val(r); {$this->callback}(r);};";

        $this->callback = 'reCaptchaCallback';

        $js[] = empty($this->expiredCallback)
            ? "var reCaptchaExpiredCallback = function(){jQuery('#{$id}').val('');};"
            : "var reCaptchaExpiredCallback = function(){jQuery('#{$id}').val(''); {$this->expiredCallback}();};";

        $this->expiredCallback = 'reCaptchaExpiredCallback';

        $view->registerJs(implode("\n", $js), View::POS_BEGIN);
    }

    /**
     * @return bool|string the language code config option for google recatpcha library url.
     */
    protected function getLanguageCode()
    {
        $language = Yii::$app->language;

        if (strpos($language, '-') === false) {
            return $language;
        }

        $except = [
            'zh-HK',
            'zh-CN',
            'zh-TW',
            'en-GB',
            'de-AT',
            'de-CH',
            'pt-BR',
            'pt-PT'
        ];

        return in_array($language, $except, false)
            ? $language
            : substr($language, 0, strpos($language, '-'));
    }
}
