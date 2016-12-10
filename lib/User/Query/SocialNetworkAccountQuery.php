<?php
namespace Da\User\Query;

use Da\User\Contracts\AuthClientInterface;
use yii\db\ActiveQuery;

class SocialNetworkAccountQuery extends ActiveQuery
{
    public function whereId($id)
    {
        return $this->andWhere(['id' => $id]);
    }

    public function whereClient(AuthClientInterface $client)
    {
        return $this->andWhere(
            [
                'provider' => $client->getId(),
                'client_id' => $client->getUserAttributes()['id']
            ]
        );
    }

    public function whereCode($code)
    {
        return $this->andWhere(['code' => md5($code)]);
    }
}
