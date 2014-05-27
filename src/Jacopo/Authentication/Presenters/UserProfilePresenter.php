<?php  namespace Jacopo\Authentication\Presenters;

use Jacopo\Library\Presenters\AbstractPresenter;

/**
 * Class UserProfilePresenter
 *
 * @author jacopo beschi jacopo@jacopobeschi.com
 */
class UserProfilePresenter extends AbstractPresenter
{
    protected $default_avatar;

    function __construct($resource)
    {
        $this->default_avatar = '/packages/jacopo/laravel-authentication-acl/images/avatar.png';
        return parent::__construct($resource);
    }

    public function avatar_src()
    {
        if(! $this->resource->avatar) return $this->default_avatar;

        return $this->getBase64ImageSrcHeader() .$this->resource->avatar;
    }

    /**
     * @return string
     */
    protected function getBase64ImageSrcHeader()
    {
        return "data:image;base64,";
    }

} 