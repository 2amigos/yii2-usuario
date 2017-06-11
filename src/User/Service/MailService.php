<?php

namespace Da\User\Service;

use Da\User\Contracts\ServiceInterface;
use Yii;
use yii\mail\BaseMailer;

class MailService implements ServiceInterface
{
    protected $viewPath = '@Da/User/resources/views/mail';

    protected $from;
    protected $to;
    protected $subject;
    protected $view;
    protected $params = [];
    protected $mailer;

    /**
     * MailService constructor.
     *
     * @param string $from
     * @param string $to
     * @param string $subject
     * @param string $view
     * @param array $params
     * @param MailerInterface $mailer
     */
    public function __construct($from, $to, $subject, $view, array $params, BaseMailer $mailer)
    {
        $this->from = $from;
        $this->to = $to;
        $this->subject = $subject;
        $this->view = $view;
        $this->params = $params;
        $this->mailer = $mailer;
        $this->mailer->setViewPath($this->viewPath);
        $this->mailer->getView()->theme = Yii::$app->view->theme;
    }

    /**
     * @param $name
     * @param $value
     *
     * @return $this
     */
    public function setViewParam($name, $value)
    {
        $this->params[$name] = $value;

        return $this;
    }

    /**
     * @return bool
     */
    public function run()
    {
        return $this->mailer
            ->compose(['html' => $this->view, 'text' => "text/{$this->view}"], $this->params)
            ->setFrom($this->from)
            ->setTo($this->to)
            ->setSubject($this->subject)
            ->send();
    }
}
