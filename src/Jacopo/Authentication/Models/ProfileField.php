<?php  namespace Jacopo\Authentication\Models;

/**
 * Class ProfileType
 *
 * @author jacopo beschi jacopo@jacopobeschi.com
 */
class ProfileField extends BaseModel
{
    protected $table = "profile_field";

    protected $fillable = ["value", "profile_id", "profile_field_type_id"];

    public function profile_field_type()
    {
        return $this->belongsTo('Jacopo\Authentication\Models\ProfileFieldType','profile_field_type_id');
    }

    public function user_profile()
    {
        return $this->belongsTo('Jacopo\Authentication\Models\UserProfile','user_profile_id');
    }
} 