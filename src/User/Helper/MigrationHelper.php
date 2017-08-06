<?php

namespace Da\User\Helper;

use RuntimeException;

class MigrationHelper
{
    /**
     * @param string $driverName
     *
     * @return null|string
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
                return null;
            default:
                throw new RuntimeException('Your database is not supported!');
        }
    }

    /**
     * @param $driverName
     *
     * @return string
     */
    public static function resolveDbType($driverName)
    {
        switch ($driverName) {
            case 'mysql':
                return $driverName;
            case 'pgsql':
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
     * @return bool
     */
    public static function isMicrosoftSQLServer($driverName)
    {
        return self::resolveDbType($driverName) == 'sqlsrv';
    }
}
