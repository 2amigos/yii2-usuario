<?php

namespace Da\User\Search;

use Da\User\Traits\AuthManagerTrait;
use Da\User\Traits\ContainerTrait;
use yii\base\Model;
use yii\data\ArrayDataProvider;
use yii\db\Query;

abstract class AbstractAuthItemSearch extends Model
{
    use AuthManagerTrait;
    use ContainerTrait;

    /**
     * @var string
     */
    public $name;
    /**
     * @var string
     */
    public $description;
    /**
     * @var string
     */
    public $rule_name;

    /**
     * @return int
     */
    abstract public function getType();

    /**
     * @return array
     */
    public function scenarios()
    {
        return [
            'default' => ['name', 'description', 'rule_name'],
        ];
    }

    public function search($params = [])
    {
        /** @var ArrayDataProvider $dataProvider */
        $dataProvider = $this->make(ArrayDataProvider::class);

        $query = (new Query())
            ->select(['name', 'description', 'rule_name'])
            ->andWhere(['type' => $this->getType()])
            ->from($this->getAuthManager()->itemTable);

        if ($this->load($params) && $this->validate()) {
            $query
                ->andFilterWhere(['like', 'name', $this->name])
                ->andFilterWhere(['like', 'description', $this->description])
                ->andFilterWhere(['like', 'rule_name', $this->rule_name]);
        }

        $dataProvider->allModels = $query->all($this->getAuthManager()->db);

        return $dataProvider;
    }
}
