<?php

/*
 * This file is part of the 2amigos/yii2-usuario project.
 *
 * (c) 2amigOS! <http://2amigos.us/>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Da\Usuario\Base\Model\Query;

use yii\db\ActiveQuery;

class ProfileQuery extends ActiveQuery
{
    /**
     * Search by user id
     *
     * @param int $id
     *
     * @return $this
     */
    public function whereUserId(int $id): self
    {
        return $this->andWhere(['user_id' => $id]);
    }
}
