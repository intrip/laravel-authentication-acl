<?php  namespace LaravelAcl\Authentication\Presenters;
/**
 * Class UserPresenter
 *
 * @author jacopo beschi jacopo@jacopobeschi.com
 */
use LaravelAcl\Authentication\Presenters\Traits\PermissionTrait;
use LaravelAcl\Library\Presenters\AbstractPresenter;

class UserPresenter extends AbstractPresenter
{
    use PermissionTrait;
} 