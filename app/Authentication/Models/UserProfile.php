<?php  namespace LaravelAcl\Authentication\Models;

use LaravelAcl\Authentication\Presenters\UserProfilePresenter;

/**
 * Class UserProfile
 *
 * @author jacopo beschi jacopo@jacopobeschi.com
 */

class UserProfile extends BaseModel
{
    protected $table = "user_profile";

    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'phone',
        'vat',
        'state',
        'city',
        'country',
        'zip',
        'code',
        'address',
        'avatar'
    ];

    protected $guarded = ["id"];

    public function user()
    {
        return $this->belongsTo('LaravelAcl\Authentication\Models\User', "user_id");
    }

    public function profile_field()
    {
        return $this->hasMany('LaravelAcl\Authentication\Models\ProfileField');
    }

    public function getAvatarAttribute()
    {
        return isset($this->attributes['avatar']) ? base64_encode($this->attributes['avatar']) : null;
    }

    public function presenter()
    {
        return new UserProfilePresenter($this);
    }
} 