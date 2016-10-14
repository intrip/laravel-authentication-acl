<?php
namespace LaravelAcl\Authentication\Repository;

/**
 * Class UserRepository
 *
 * @author jacopo beschi jacopo@jacopobeschi.com
 */
use App;
use Cartalyst\Sentry\Users\UserExistsException as CartaUserExists;
use Cartalyst\Sentry\Users\UserNotFoundException;
use DateTime;
use Event;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Config;
use LaravelAcl\Authentication\Exceptions\UserExistsException;
use LaravelAcl\Authentication\Exceptions\UserNotFoundException as NotFoundException;
use LaravelAcl\Authentication\Repository\Interfaces\UserRepositoryInterface;
use LaravelAcl\Library\Repository\EloquentBaseRepository;

class SentryUserRepository extends EloquentBaseRepository implements UserRepositoryInterface
{
    /**
     * Sentry instance
     *
     * @var
     */
    protected $sentry;

    protected $groupModel =  \LaravelAcl\Authentication\Models\Group::class;
    protected $userModel =  \LaravelAcl\Authentication\Models\User::class;

    public function __construct()
    {
        $this->sentry = App::make('sentry');

        if (method_exists($this->sentry, 'getGroupProvider')) {
            $this->groupModel = get_class( $this->sentry->getGroupProvider()->createModel());
        }

        if (method_exists($this->sentry, 'getUserProvider')) {
            $this->userModel = get_class ($this->sentry->getUserProvider()->createModel());
        }

        return parent::__construct( new $this->userModel );
    }

    /**
     * Create a new object
     *
     * @return mixed
     * @override
     */
    public function create(array $input)
    {
        $data = array(
                "email"     => $input["email"],
                "password"  => $input["password"],
                "activated" => $input["activated"],
                "banned"    => isset($input["banned"]) ? $input["banned"] : 0
        );

        try
        {
            $user = $this->sentry->createUser($data);
        } catch(CartaUserExists $e)
        {
            throw new UserExistsException;
        }

        return $user;
    }

    /**
     * Update a new object
     *
     * @param       id
     * @param array $data
     * @throws \LaravelAcl\Authentication\Exceptions\UserNotFoundException
     * @return mixed
     * @override
     */
    public function update($id, array $data)
    {
        $this->ClearEmptyPassword($data);
        $obj = $this->find($id);
        Event::fire('repository.updating', [$obj]);
        $obj->update($data);
        return $obj;
    }

    /**
     * @override
     * @param array $input_filter
     * @return mixed
     */
    public function all(array $input_filter = [], $user_repository_search = null)
    {
        $per_page = Config::get('acl_base.users_per_page');
        $user_repository_search = $user_repository_search ? $user_repository_search : new UserRepositorySearchFilter($per_page);
        return $user_repository_search->all($input_filter);
    }

    /**
     * @param array $data
     * @return array
     */
    protected function ClearEmptyPassword(array &$data)
    {
        if(empty($data["password"])) unset($data["password"]);
    }

    /**
     * Add a group to the user
     *
     * @param $id group id
     * @throws \LaravelAcl\Authentication\Exceptions\UserNotFoundException
     */
    public function addGroup($user_id, $group_id)
    {
        try
        {
            $group = new $this->groupModel;
            $group = $group->findOrFail($group_id);
            $user = $this->find($user_id);

            $user->addGroup($group);
        } catch(ModelNotFoundException $e)
        {
            throw new NotFoundException;
        }
    }

    /**
     * Remove a group to the user
     *
     * @param $id group id
     * @throws \LaravelAcl\Authentication\Exceptions\UserNotFoundException
     */
    public function removeGroup($user_id, $group_id)
    {
        try
        {
            $group = new $this->groupModel;
            $group = $group->findOrFail($group_id);
            $user = $this->find($user_id);
            $user->removeGroup($group);
        } catch(ModelNotFoundException $e)
        {
            throw new NotFoundException;
        }
    }

    /**
     * Activates a user
     *
     * @param string login_name
     * @return mixed
     * @throws \LaravelAcl\Library\Exceptions\NotFoundException
     */
    public function activate($login_name)
    {
        $user = $this->findByLogin($login_name);

        $user->activation_code = null;
        $user->activated = true;
        $user->activated_at = new DateTime;
        return $user->save();
    }

    /**
     * @param $login_name
     * @throws \LaravelAcl\Authentication\Exceptions\UserNotFoundException
     */
    public function findByLogin($login_name)
    {
        try
        {
            $user = $this->sentry->findUserByLogin($login_name);
        } catch(UserNotFoundException $e)
        {
            throw new NotFoundException;
        }

        return $user;
    }

    /**
     * Obtain a list of user from a given group
     *
     * @param String $group_name
     * @throws \LaravelAcl\Authentication\Exceptions\UserNotFoundException
     * @return mixed
     */
    public function findFromGroupName($group_name)
    {
        $group = $this->sentry->findGroupByName($group_name);
        if(!$group) throw new UserNotFoundException;

        return $group->users;
    }

    public function allModel()
    {
        return $this->model->all();
    }
}