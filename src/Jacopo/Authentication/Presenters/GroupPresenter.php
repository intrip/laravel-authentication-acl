<?php  namespace Jacopo\Authentication\Presenters;
/**
 * Class GroupPresenter
 *
 * @author jacopo beschi jacopo@jacopobeschi.com
 */
use Jacopo\Authentication\Presenters\Traits\PermissionTrait;
use Jacopo\Library\Presenters\AbstractPresenter;

class GroupPresenter extends AbstractPresenter
{
    use PermissionTrait;
} 