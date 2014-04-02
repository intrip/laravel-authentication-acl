<?php
namespace Jacopo\Authentication\Repository;
/**
 * Class UserRepository
 *
 * @author jacopo beschi jacopo@jacopobeschi.com
 */
use DateTime;
use Jacopo\Authentication\Repository\Interfaces\UserRepositoryInterface;
use Jacopo\Library\Repository\EloquentBaseRepository;
use Jacopo\Library\Repository\Interfaces\BaseRepositoryInterface;
use Jacopo\Authentication\Exceptions\UserNotFoundException as NotFoundException;
use Jacopo\Authentication\Exceptions\UserExistsException;
use Jacopo\Authentication\Models\User;
use Jacopo\Authentication\Models\Group;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Cartalyst\Sentry\Users\UserExistsException as CartaUserExists;
use Cartalyst\Sentry\Users\UserNotFoundException;
use Event, App, DB;
use Illuminate\Support\Facades\Config;

class SentryUserRepository extends EloquentBaseRepository implements UserRepositoryInterface
{
    /**
     * Sentry instance
     * @var
     */
    protected $sentry;
    /**
     * Config file reader
     * @var Mixed
     */
    protected $config;

    public function __construct($config = null)
    {
        $this->sentry = \App::make('sentry');
        $this->config = $config ? $config : App::make('config');
        return parent::__construct(new User);
    }

    /**
     * Create a new object
     * @return mixed
     * @override
     * @todo db test
     */
    public function create(array $input)
    {
        $data = array(
                "email" => $input["email"],
                "password" => $input["password"],
                "activated" => $input["activated"],
        );

        try
        {
            $user = $this->sentry->createUser($data);
        }
        catch(CartaUserExists $e)
        {
            throw new UserExistsException;
        }

        return $user;
    }

    /**
     * Update a new object
     * @param id
     * @param array $data
     * @throws \Jacopo\Authentication\Exceptions\UserNotFoundException
     * @return mixed
     * @override
     * @todo db test
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
     * @param array $data
     * @return array
     */
    protected function ClearEmptyPassword(array &$data)
    {
        if (empty($data["password"])) unset($data["password"]);
    }

    /**
     * Add a group to the user
     * @param $id group id
     * @throws \Jacopo\Authentication\Exceptions\UserNotFoundException
     * @todo test
     */
    public function addGroup($user_id, $group_id)
    {
        try
        {
            $group = Group::findOrFail($group_id);
            $user = User::findOrFail($user_id);
            $user->addGroup($group);
        }
        catch(ModelNotFoundException $e)
        {
            throw new NotFoundException;
        }
    }
    /**
     * Remove a group to the user
     * @param $id group id
     * @throws \Jacopo\Authentication\Exceptions\UserNotFoundException
     * @todo test
     */
    public function removeGroup($user_id, $group_id)
    {
        try
        {
            $group = Group::findOrFail($group_id);
            $user = User::findOrFail($user_id);
            $user->removeGroup($group);
        }
        catch(ModelNotFoundException $e)
        {
            throw new NotFoundException;
        }
    }

    /**
     * Obtain a list of user from a given group
     *
     * @param String $group_name
     * @throws \Palmabit\Authentication\Exceptions\UserNotFoundException
     * @return mixed
     */
    public function findFromGroupName($group_name)
    {
        $group = $this->sentry->findGroupByName($group_name);
        if(! $group) throw new UserNotFoundException;

        return $group->users;
    }

    /**
     * Activates a user
     *
     * @param string login_name
     * @return mixed
     * @throws \Jacopo\Library\Exceptions\NotFoundException
     */
    public function activate($login_name)
    {
        $user = $this->findByLogin($login_name);

        $user->activation_code = null;
        $user->activated       = true;
        $user->activated_at    = new DateTime;
        return $user->save();
    }

    /**
     * Deactivate a user
     *
     * @param $id
     * @return mixed
     */
    public function deactivate($id)
    {
        // TODO: Implement deactivate() method.
    }

    /**
     * Suspends a user
     *
     * @param $id
     * @param $duration in minutes
     * @return mixed
     */
    public function suspend($id, $duration)
    {
        // TODO: Implement suspend() method.
    }

    /**
     * @param $login_name
     * @throws \Jacopo\Authentication\Exceptions\UserNotFoundException
     */
    public function findByLogin($login_name)
    {
        try
        {
            $user = $this->sentry->findUserByLogin($login_name);
        }
        catch(UserNotFoundException $e)
        {
            throw new NotFoundException;
        }

        return $user;
    }

    /**
     * @override
     * @param array $input_filter
     * @return mixed|void
     */
    public function all(array $input_filter = [])
    {
        $results_per_page = $this->config->get('authentication::users_per_page');
        // set table names
        $user_table_name = $this->model->getTable();
        $profile_table_name = App::make('profile_repository')->getModel()->getTable();
        $user_groups_table_name = "users_groups";
        $group_table_name = 'groups';

        $q = $this->createTableJoins($user_table_name, $profile_table_name, $user_groups_table_name, $group_table_name);

        $q = $this->applySearchFilters($input_filter, $q, $user_table_name, $profile_table_name, $group_table_name);

        $q = $this->applyOrderingFilter($input_filter, $q);

        $q = $this->createAllSelect($q, $user_table_name, $profile_table_name, $group_table_name);

        return $q->paginate($results_per_page);
    }

    /**
     * @param array $input_filter
     * @param       $q
     * @param       $user_table
     * @param       $profile_table
     * @pram        $group_table
     * @return mixed
     */
    protected function applySearchFilters(array $input_filter = null, $q, $user_table, $profile_table , $group_table)
    {
        if($input_filter) foreach ($input_filter as $column => $value) {
            if( $value !== '') switch ($column) {
                case 'activated':
                    $q = $q->where($user_table . '.activated', '=', $value);
                    break;
                case 'email':
                    $q = $q->where($user_table . '.email', 'LIKE', "%{$value}%");
                    break;
                case 'first_name':
                    $q = $q->where($profile_table . '.first_name', 'LIKE', "%{$value}%");
                    break;
                case 'last_name':
                    $q = $q->where($profile_table . '.last_name', 'LIKE', "%{$value}%");
                    break;
                case 'zip':
                    $q = $q->where($profile_table . '.zip', '=', $value);
                    break;
                case 'code':
                    $q = $q->where($profile_table . '.code', '=', $value);
                    break;
                case 'group_id':
                    $q = $q->where($group_table . '.id' , '=', $value);
            }
        }

        return $q;
    }

    /**
     * @param $q
     * @param $user_table_name
     * @param $profile_table_name
     * @param $group_table_name
     * @return mixed
     */
    protected function createAllSelect($q, $user_table_name, $profile_table_name, $group_table_name)
    {
        $q = $q->select($user_table_name . '.*',
            $profile_table_name . '.first_name',
            $profile_table_name . '.last_name',
            $profile_table_name . '.zip',
            $profile_table_name . '.code',
            $group_table_name . '.name'
        );

        return $q;
    }

    /**
     * @param array $input_filter
     * @param       $q
     * @return mixed
     */
    protected function applyOrderingFilter(array $input_filter, $q)
    {
        if (isset($input_filter['order_by']) && ! empty($input_filter['order_by']) )
        {
            // get ordering type
            $ordering = (isset($input_filter['ordering']) && $input_filter['ordering'] == 'asc') ? 'ASC' : 'DESC';
            $q = $q->orderBy($input_filter['order_by'], $ordering);
        }

        return $q;
    }

    /**
     * @param $user_table_name
     * @param $profile_table_name
     * @param $user_groups_table_name
     * @param $group_table_name
     * @return mixed
     */
    protected function createTableJoins($user_table_name, $profile_table_name, $user_groups_table_name, $group_table_name)
    {
        $q = DB::table($user_table_name)
            ->leftJoin($profile_table_name, $user_table_name . '.id', '=', $profile_table_name . '.user_id')
            ->leftJoin($user_groups_table_name, $user_table_name . '.id', '=', $user_groups_table_name . '.user_id')
            ->leftJoin($group_table_name, $user_groups_table_name . '.group_id', '=',$group_table_name . '.id');

        return $q;
    }
}