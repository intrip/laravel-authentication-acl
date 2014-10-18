<?php  namespace Jacopo\Authentication\Controllers;
/**
 * Class PermissionController
 *
 * @author jacopo beschi jacopo@jacopobeschi.com
 */
use Jacopo\Library\Form\FormModel;
use Jacopo\Authentication\Models\Permission;
use Jacopo\Authentication\Validators\PermissionValidator;
use Jacopo\Library\Exceptions\JacopoExceptionsInterface;
use View, Input, Redirect, App, Config;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class PermissionController extends \Controller
{
    /**
     * @var \Jacopo\Authentication\Repository\PermissionGroupRepository
     */
    protected $r;
    /**
     * @var \Jacopo\Authentication\Validators\PermissionValidator
     */
    protected $v;

    public function __construct(PermissionValidator $v)
    {
        $this->r = App::make('permission_repository');
        $this->v = $v;
        $this->f = new FormModel($this->v, $this->r);
    }

    public function getList()
    {
        $objs = $this->r->all();

        return View::make('laravel-authentication-acl::admin.permission.list')->with(["permissions" => $objs]);
    }

    public function editPermission()
    {
        try
        {
            $obj = $this->r->find(Input::get('id'));
        }
        catch(JacopoExceptionsInterface $e)
        {
            $obj = new Permission;
        }

        return View::make('laravel-authentication-acl::admin.permission.edit')->with(["permission" => $obj]);
    }

    public function postEditPermission()
    {
        $id = Input::get('id');

        try
        {
            $obj = $this->f->process(Input::all());
        }
        catch(JacopoExceptionsInterface $e)
        {
            $errors = $this->f->getErrors();
            // passing the id incase fails editing an already existing item
            return Redirect::route("permission.edit", $id ? ["id" => $id]: [])->withInput()->withErrors($errors);
        }

        return Redirect::action('Jacopo\Authentication\Controllers\PermissionController@editPermission',["id" => $obj->id])->withMessage(Config::get('laravel-authentication-acl::messages.flash.success.permission_permission_edit_success'));
    }

    public function deletePermission()
    {
        try
        {
            $this->f->delete(Input::all());
        }
        catch(JacopoExceptionsInterface $e)
        {
            $errors = $this->f->getErrors();
            return Redirect::action('Jacopo\Authentication\Controllers\PermissionController@getList')->withErrors($errors);
        }
        return Redirect::action('Jacopo\Authentication\Controllers\PermissionController@getList')->withMessage(Config::get('laravel-authentication-acl::messages.flash.success.permission_permission_delete_success'));
    }
} 