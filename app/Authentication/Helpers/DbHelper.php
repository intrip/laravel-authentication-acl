<?php  namespace Jacopo\Authentication\Helpers;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use PDO;

class DbHelper
{
    /**
     * Drivers that doesn't support foreign keys check
     * @var array
     */
    public static $no_foreign_keys_drivers = ['sqlite', 'pgsql'];

    public static function startTransaction()
    {
        static::getConnection()->getPdo()->beginTransaction();
        static::stopForeignKeysCheck();
    }

    public static function stopForeignKeysCheck()
    {
        $current_driver = static::getCurrentDriverName();
        if (self::supportForeignKeysCheck($current_driver)) {
            static::getConnection()->getPdo()->exec('SET FOREIGN_KEY_CHECKS=0;');
        }
    }

    public static function startForeignKeysCheck()
    {
        $current_driver = static::getCurrentDriverName();
        if (self::supportForeignKeysCheck($current_driver)) {
            static::getConnection()->getPdo()->exec('SET FOREIGN_KEY_CHECKS=1;');
        }
    }
    
    public static function commit()
    {
        static::getConnection()->getPdo()->commit();
        static::startForeignKeysCheck();
    }

    public static function rollback()
    {
        static::getConnection()->getPdo()->rollback();
        static::startForeignKeysCheck();
    }

    /**
     * @return string
     */
    public static function getConnectionName()
    {
        return (App::environment() != 'testing') ? 'authentication' : '';
    }

    public static function getConnection()
    {
        return DB::connection(static::getConnectionName());
    }

    /**
     * @return mixed
     */
    protected static function getCurrentDriverName()
    {
        $current_driver = static::getConnection()->getPdo()->getAttribute(PDO::ATTR_DRIVER_NAME);
        return $current_driver;
    }

    /**
     * @param $current_driver
     * @return bool
     */
    protected static function supportForeignKeysCheck($current_driver)
    {
        return ! in_array($current_driver, static::$no_foreign_keys_drivers);
    }

}