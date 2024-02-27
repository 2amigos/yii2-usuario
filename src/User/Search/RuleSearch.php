<?php

/*
 * This file is part of the 2amigos/yii2-usuario project.
 *
 * (c) 2amigOS! <http://2amigos.us/>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Da\User\Search;

use Da\User\Exception\NotImplementedException;
use Da\User\Model\Rule;
use Da\User\Traits\ContainerAwareTrait;
use yii\base\InvalidConfigException;
use yii\base\InvalidParamException;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Query;
use yii\rbac\DbManager;

class RuleSearch extends Rule
{
    use ContainerAwareTrait;

    /**
     * @var string
     */
    public $created_at;

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return Model::scenarios();
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['name', 'string'],
        ];
    }

    /**
     * @param array $params
     *
     * @throws InvalidConfigException
     * @throws InvalidParamException
     * @return ActiveDataProvider
     */
    public function search(array $params = [])
    {
        $authManager = $this->getAuthManager();
        if(!($authManager instanceof DbManager)) {
            throw new NotImplementedException();
        }
        $query = (new Query())
            ->select(['name', 'data', 'created_at', 'updated_at'])
            ->from($authManager->ruleTable)
            ->orderBy(['name' => SORT_ASC]);

        if ($this->load($params)) {
            $query->andFilterWhere(['name' => $this->name]);
        }

        if (!$this->validate()) {
            $query->where('0=1');
        }

        return $this->make(
            ActiveDataProvider::class,
            [],
            [
                'query' => $query,
                'db' => $authManager->db,
                'sort' => [
                    'attributes' => ['name', 'created_at', 'updated_at']
                ]
            ]
        );
    }
}
