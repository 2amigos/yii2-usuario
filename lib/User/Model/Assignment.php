<?php

namespace Da\User\Model;

use Da\User\Traits\AuthManagerTrait;
use Da\User\Validator\RbacItemsValidator;
use yii\base\InvalidConfigException;
use yii\base\Model;
use Yii;

class Assignment extends Model
{
    use AuthManagerTrait;

    public $items = [];
    public $user_id;
    public $updated = false;

    /**
     * {@inheritdoc}
     *
     * @throws InvalidConfigException
     */
    public function init()
    {
        parent::init();

        if ($this->user_id === null) {
            throw new InvalidConfigException('"user_id" must be set.');
        }

        $this->items = array_keys($this->getAuthManager()->getItemsByUser($this->user_id));
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'items' => Yii::t('user', 'Items'),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['user_id', 'required'],
            ['items', RbacItemsValidator::class],
            ['user_id', 'integer'],
        ];
    }
}
