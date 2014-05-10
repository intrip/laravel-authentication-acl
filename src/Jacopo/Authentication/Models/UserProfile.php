<?php  namespace Jacopo\Authentication\Models;
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
        'address'
    ];

    protected $guarded = ["id"];

    public function user()
    {
        return $this->belongsTo('Jacopo\Authentication\Models\User', "user_id");
    }

    public function profile_field()
    {
        return $this->hasMany('Jacopo\Authentication\Models\ProfileField');
    }
} 