<?php  namespace Jacopo\Authentication\Models;
/**
 * Class Group
 *
 * @author jacopo beschi jacopo@jacopobeschi.com
 */
use Cartalyst\Sentry\Groups\Eloquent\Group as SentryGroup;
use Jacopo\Authentication\Classes\Traits\OverrideConnectionTrait;

class Group extends SentryGroup
{
    use OverrideConnectionTrait;

    protected $guarded = ["id"];

    protected $fillable = ["name", "permissions", "editable"];
} 