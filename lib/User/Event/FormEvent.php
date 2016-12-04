<?php
namespace Da\User\Event;

use yii\base\Event;
use yii\base\Model;

/**
 *
 * FormEvent.php
 *
 * Date: 4/12/16
 * Time: 15:11
 * @author Antonio Ramirez <hola@2amigos.us>
 */
class FormEvent extends Event
{
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

