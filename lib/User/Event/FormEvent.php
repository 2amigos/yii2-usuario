<?php
namespace Da\User\Event;

use yii\base\Event;
use yii\base\Model;

class FormEvent extends Event
{
    const EVENT_BEFORE_REQUEST = 'beforeRequest';
    const EVENT_AFTER_REQUEST = 'afterRequest';

    protected $form;

    public function __construct(Model $form, array $config = [])
    {
        $this->form = $form;
        parent::__construct($config);
    }

    public function getForm()
    {
        return $this->form;
    }
}

