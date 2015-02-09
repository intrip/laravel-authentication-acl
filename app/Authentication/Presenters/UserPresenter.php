<?php  namespace Jacopo\Authentication\Presenters;
/**
 * Class UserPresenter
 *
 * @author jacopo beschi jacopo@jacopobeschi.com
 */
use Jacopo\Authentication\Presenters\Traits\PermissionTrait;
use Jacopo\Library\Presenters\AbstractPresenter;

class UserPresenter extends AbstractPresenter
{
    use PermissionTrait;
} 