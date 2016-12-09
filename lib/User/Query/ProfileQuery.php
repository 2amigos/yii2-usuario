<?php
namespace Da\User\Query;

use yii\db\ActiveQuery;

class ProfileQuery extends ActiveQuery
{
    public function whereId($id)
    {
        return $this->andWhere(['id' => $id]);
    }
}
