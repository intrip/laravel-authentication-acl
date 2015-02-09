<?php  namespace Jacopo\Authentication\Classes\CustomProfile; 
/**
 * Class customProfileFormHelper
 *
 * @author jacopo beschi jacopo@jacopobeschi.com
 */
class customProfileFormHelper 
{
    protected $custom_profile_repository;

    public function __construct($custom_profile = null)
    {
        $this->custom_profile_repository = $custom_profile ? $custom_profile : new CustomProfileRepository();
    }
    
} 