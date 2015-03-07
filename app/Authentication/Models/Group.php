<?php  namespace LaravelAcl\Authentication\Models;
/**
 * Class Group
 *
 * @author jacopo beschi jacopo@jacopobeschi.com
 */
use Cartalyst\Sentry\Groups\Eloquent\Group as SentryGroup;
use LaravelAcl\Library\Traits\OverrideConnectionTrait;

class Group extends SentryGroup
{
    use OverrideConnectionTrait;

    protected $guarded = ["id"];

    protected $fillable = ["name", "permissions", "protected"];
} 