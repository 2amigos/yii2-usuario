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

use RuntimeException;

class MigrationHelper
{
    /**
     * @param string $driverName
     *
     * @throws RuntimeException
     * @return null|string
     *
     */
    public static function resolveTableOptions($driverName)
    {
        switch ($driverName) {
            case 'mysql':
                return 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
            case 'pgsql':
            case 'dblib':
            case 'mssql':
            case 'sqlsrv':
            case 'sqlite':
                return null;
            default:
                throw new RuntimeException('Your database is not supported!');
        }
    }

    /**
     * @param $driverName
     *
     * @throws RuntimeException
     * @return string
     *
     */
    public static function resolveDbType($driverName)
    {
        switch ($driverName) {
            case 'mysql':
            case 'pgsql':
            case 'sqlite':
                return $driverName;
            case 'dblib':
            case 'mssql':
            case 'sqlsrv':
                return 'sqlsrv';
            default:
                throw new RuntimeException('Your database is not supported!');
        }
    }

    /**
     * @param string $driverName
     *
     * @throws RuntimeException
     * @return bool
     *
     */
    public static function isMicrosoftSQLServer($driverName)
    {
        return self::resolveDbType($driverName) === 'sqlsrv';
    }

    /**
     * @param $driverName
     * @param bool $value
     *
     * @return bool|int
     */
    public static function getBooleanValue($driverName, $value = false)
    {
        if (self::isMicrosoftSQLServer($driverName)) {
            return $value ? 1 : 0;
        }
        return $value;
    }
}
