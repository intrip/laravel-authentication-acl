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
        'code',
        'first_name',
        'last_name',
        'phone',
        'cf',
        'billing_address',
        'billing_address_zip',
        'shipping_address',
        'shipping_address_zip',
        'billing_state',
        'billing_city',
        'billing_country',
        'shipping_state',
        'shipping_city',
        'shipping_country'
    ];

    protected $guarded = ["id"];

    public function user()
    {
        return $this->belongsTo('Jacopo\Authentication\Models\User', "user_id");
    }
} 