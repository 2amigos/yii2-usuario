<?php

/*
 * This file is part of the 2amigos/yii2-usuario project.
 *
 * (c) 2amigOS! <http://2amigos.us/>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Da\User\Query;

use Da\User\Contracts\AuthClientInterface;
use yii\db\ActiveQuery;

class SocialNetworkAccountQuery extends ActiveQuery
{
    public function whereId($id)
    {
        // If $this already contains a condition for 'id', remove it
        // to avoid multiple ANDed 'id' conditions.
        // @ref https://github.com/2amigos/yii2-usuario/issues/568
        if (is_array($this->where)) {
            foreach ($this->where as $idx => $arrCondition) {
                if (isset($arrCondition['id'])) {
                    array_splice($this->where, $idx, 1);
                    break;
                }
            }
        }

        return $this->andWhere(['id' => $id]);
    }

    public function whereClient(AuthClientInterface $client)
    {
        return $this->andWhere(
            [
                'provider' => $client->getId(),
                'client_id' => (string)$client->getUserId(),
            ]
        );
    }

    public function whereCode($code)
    {
        return $this->andWhere(['code' => md5($code)]);
    }
}
