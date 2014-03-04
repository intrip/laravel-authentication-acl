<?php namespace Jacopo\Authentication\Validators;

use Event;
use Jacopo\Library\Validators\OverrideConnectionValidator;

class UserProfileValidator extends OverrideConnectionValidator
{
    protected static $rules = array(
        "first_name" => "max:50",
        "code" => "max:50",
        "last_name" => "max:50",
        "phone" => "max:20",
        "vat" => "max:50",
        "cf" => "max:50",
        'billing_address' => "max:50",
        'billing_address_zip' => "max:50",
        'shipping_address' => "max:50",
        'shipping_address_zip' => "max:50",
        'billing_state' => "max:50",
        'billing_city' => "max:50",
        'billing_country' => "max:50",
        'shipping_state' => "max:50",
        'shipping_city' => "max:50",
        'shipping_country' => "max:50"
    );
} 