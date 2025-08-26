<?php

/*
 * This file is part of the 2amigos/yii2-usuario project.
 *
 * (c) 2amigOS! <http://2amigos.us/>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Da\User\Helper;

use Da\User\Module;
use DateTime;
use DateTimeZone;
use Yii;
use yii\base\InvalidParamException;
use yii\helpers\ArrayHelper;

class TimezoneHelper
{
    /**
     * Get all of the time zones with the offsets sorted by their offset.
     *
     * @throws InvalidParamException
     * @return array
     *
     */
    public static function getAll()
    {
        $timeZones = [];
        $timeZoneIdentifiers = DateTimeZone::listIdentifiers();

        foreach ($timeZoneIdentifiers as $timeZone) {
            $date = new DateTime('now', new DateTimeZone($timeZone));
            $offset = $date->getOffset() / 60 / 60;
            $timeZones[] = [
                'timezone' => $timeZone,
                'name' => "{$timeZone} (UTC " . ($offset > 0 ? '+' : '') . "{$offset})",
                // As of PHP 8.1 array keys cannot be float. Offset is used for sorting only
                'offset' => $offset * 100,
            ];
        }
        ArrayHelper::multisort($timeZones, 'offset', SORT_DESC, SORT_NUMERIC);
        return $timeZones;
    }
}
