<?php  namespace Jacopo\Authentication\Helpers;

use Illuminate\Support\Facades\DB;
use PDO;

class DbHelper
{

    protected static $connection_name = 'authentication';

    public static function startTransaction()
    {
        DB::connection(static::$connection_name)->getPdo()->beginTransaction();
        static::stopForeignKeysCheck();
    }

    protected function stopForeignKeysCheck()
    {
        $current_driver = DB::connection(static::$connection_name)->getPdo()->getAttribute(PDO::ATTR_DRIVER_NAME);
        if ($current_driver != 'sqlite') {
            //@todo here stop foreign keys and also
            DB::connection(static::$connection_name)->getPdo()->exec('SET FOREIGN_KEY_CHECKS=0;');

        }
    }

    /**
     * @return string
     */
    public static function getConnectionName()
    {
        return self::$connection_name;
    }

}