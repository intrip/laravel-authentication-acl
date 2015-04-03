<?php  namespace LaravelAcl\Authentication\Models;
/**
 * Class Group
 *
 * @author jacopo beschi jacopo@jacopobeschi.com
 */
use Cartalyst\Sentry\Groups\Eloquent\Group as SentryGroup;

class Group extends SentryGroup
{
    protected $guarded = ["id"];

    protected $fillable = ["name", "permissions", "protected"];
} 