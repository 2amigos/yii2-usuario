<?php

namespace Da\User\Service;

use Adldap\Models\User;
use Da\User\Contracts\ServiceInterface;
use Da\User\Traits\ModuleAwareTrait;
use yii\helpers\ArrayHelper;

class InitLdapUserService implements ServiceInterface
{
    use ModuleAwareTrait;

    protected $model;
    protected $attributes;
    protected $ldapUser;

    public function __construct($model, array $attributes, ?User $ldapUser)
    {
        $this->model = $model;
        $this->attributes = $attributes;
        $this->ldapUser = $ldapUser;
    }

    /**
     * @inheritDoc
     */
    public function run()
    {
        foreach ($this->attributes as $attribute => $ldapAttribute) {
            // if Closure call and assign
            if ($ldapAttribute instanceof \Closure) {
                $this->model->$attribute = $ldapAttribute($this->ldapUser, $attribute);
                continue;
            }
            $value = $this->ldapUser->$ldapAttribute;
            if (empty($value)) {
                continue;
            }
            $this->model->$attribute = ArrayHelper::getValue($value, 0);
        }
    }
}
