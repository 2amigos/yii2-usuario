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

use yii\db\ActiveQuery;

class ProfileQuery extends ActiveQuery
{
    /**
     * Search by user id
     * @param  mixed       $user_id
     * @return ActiveQuery
     */
    public function whereUserId($user_id)
    {
        return $this->andWhere(['user_id' => $user_id]);
    }
}
