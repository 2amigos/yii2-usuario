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

use Da\User\Model\SessionHistory;
use Da\User\Traits\ContainerAwareTrait;
use yii\base\InvalidConfigException;
use yii\base\InvalidParamException;
use yii\data\ActiveDataProvider;

class SessionHistorySearch extends SessionHistory
{
    use ContainerAwareTrait;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_agent', 'ip'], 'safe'],
        ];
    }

    /**
     * @param array $params
     *
     * @throws InvalidConfigException
     * @throws InvalidParamException
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = SessionHistory::find()->andWhere([
            'user_id' => $this->user_id,
        ]);

        /** @var ActiveDataProvider $dataProvider */
        $dataProvider = $this->make(
            ActiveDataProvider::class,
            [],
            [
                'query' => $query,
                'sort' => [
                    'defaultOrder' => [
                        'updated_at' => SORT_DESC
                    ],
                ]
            ]
        );

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere(['like', 'user_agent', $this->user_agent])
            ->andFilterWhere(['like', 'ip', $this->ip]);

        return $dataProvider;
    }
}
