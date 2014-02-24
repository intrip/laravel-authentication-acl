<?php  namespace Jacopo\Authentication\Models;
/**
 * Class BaseModel
 *
 * @author jacopo beschi jacopo@jacopobeschi.com
 */
use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
    /**
     * @override
     * @return \Illuminate\Database\Connection
     */
    public function getConnection()
    {
        return static::resolveConnection('authentication');
    }
} 