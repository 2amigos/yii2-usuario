<?php

/*
 * This file is part of the 2amigos/yii2-usuario project.
 *
 * (c) 2amigOS! <http://2amigos.us/>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Da\User\Model;

use Da\User\Traits\AuthManagerAwareTrait;
use Da\User\Validator\RbacRuleNameValidator;
use Da\User\Validator\RbacRuleValidator;
use Yii;
use yii\base\Model;

class Rule extends Model
{
    use AuthManagerAwareTrait;

    /**
     * @var string
     */
    public $name;
    /**
     * @var string fully qualified class name. Not to be confused with className() method
     */
    public $className;
    /**
     * @var string holds the name of the rule previous update
     */
    public $previousName;

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return [
            'create' => ['name', 'className'],
            'update' => ['name', 'className', 'previousName'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'className'], 'trim'],
            [['name', 'className'], 'required'],
            [['name', 'previousName'], 'match', 'pattern' => '/^\w[\w.:\-]+\w$/'],
            [['name'], RbacRuleNameValidator::class, 'previousName' => $this->previousName],
            [['className'], RbacRuleValidator::class],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'name' => Yii::t('usuario', 'Name'),
            'className' => Yii::t('usuario', 'Rule class name'),
        ];
    }
}
