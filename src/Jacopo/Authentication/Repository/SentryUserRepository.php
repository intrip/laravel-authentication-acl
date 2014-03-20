<?php
namespace Jacopo\Authentication\Repository;
/**
 * Class UserRepository
 *
 * @author jacopo beschi jacopo@jacopobeschi.com
 */
use Jacopo\Authentication\Repository\Interfaces\UserRepositoryInterface;
use Jacopo\Library\Repository\EloquentBaseRepository;
use Jacopo\Library\Repository\Interfaces\BaseRepositoryInterface;
use Jacopo\Authentication\Exceptions\UserNotFoundException as NotFoundException;
use Jacopo\Authentication\Exceptions\UserExistsException;
use Jacopo\Authentication\Models\User;
use Jacopo\Authentication\Models\Group;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Cartalyst\Sentry\Users\UserExistsException as CartaUserExists;
use Event;

class SentryUserRepository extends EloquentBaseRepository implements UserRepositoryInterface
{
    /**
     * Sentry instance
     * @var
     */
    protected $sentry;

    public function __construct()
    {
        $this->sentry = \App::make('sentry');
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
     * @param integer id
     * @return mixed
     * @throws \Jacopo\Library\Exceptions\NotFoundException
     */
    public function activate($id)
    {
        return $this->find($id)->update(["activated" => true]);
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
}