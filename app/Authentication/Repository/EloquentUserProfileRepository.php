<?php  namespace LaravelAcl\Authentication\Repository;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use LaravelAcl\Authentication\Classes\Images\ImageHelperTrait;
use LaravelAcl\Authentication\Exceptions\UserNotFoundException;
use LaravelAcl\Authentication\Exceptions\ProfileNotFoundException;
use LaravelAcl\Authentication\Repository\Interfaces\UserProfileRepositoryInterface;
use LaravelAcl\Library\Repository\EloquentBaseRepository;
use LaravelAcl\Library\Repository\Interfaces\BaseRepositoryInterface;
use App;

/**
 * Class EloquentUserProfileRepository
 *
 * @author jacopo beschi jacopo@jacopobeschi.com
 */
class EloquentUserProfileRepository extends EloquentBaseRepository implements UserProfileRepositoryInterface
{
    protected $userprofile = 'LaravelAcl\Authentication\Models\UserProfile';

    protected $user_repo;

    use ImageHelperTrait;

    /**
     * We use the user profile as a model
     */
    public function __construct()
    {
        $this->user_repo = App::make('user_repository');

        $config = config('cartalyst.sentry');
        if (isset($config['users_profile']) && isset($config['users_profile']['model'])) {
            $this->userprofile = $config['users_profile']['model'];
        }
        return parent::__construct(new $this->userprofile);
    }

    public function getFromUserId($user_id)
    {
        // checks if the user exists
        try {
            $this->user_repo->getModel()->findOrFail($user_id);
        } catch (ModelNotFoundException $e) {
            throw new UserNotFoundException;
        }
        // gets the profile
        $profile = $this->model->where('user_id', '=', $user_id)
            ->get();

        // check if the profile exists
        if ($profile->isEmpty()) throw new ProfileNotFoundException;

        return $profile->first();
    }

    public function updateAvatar($id, $input_name = "avatar")
    {
        $model = $this->find($id);
        $model->update([
            "avatar" => static::getBinaryData('170', $input_name)
        ]);
    }

    public function attachEmptyProfile($user)
    {
        if($this->hasAlreadyAnUserProfile($user)) return;

        return $this->create(["user_id" => $user->id]);
    }

    /**
     * @param $user
     * @return mixed
     */
    protected function hasAlreadyAnUserProfile($user) {
        return $user->user_profile()->count();
    }
}