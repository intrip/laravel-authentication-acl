<?php  namespace Jacopo\Authentication\Models;
/**
 * Class BaseModel
 *
 * @author jacopo beschi jacopo@jacopobeschi.com
 */
use Illuminate\Database\Eloquent\Model;
use Jacopo\Authentication\Classes\Traits\OverrideConnectionTrait;

class BaseModel extends Model
{
    use OverrideConnectionTrait;
} 