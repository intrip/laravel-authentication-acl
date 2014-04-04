<?php  namespace Jacopo\Authentication\Services;
/**
 * Class UserProfileService
 *
 * @author jacopo beschi jacopo@jacopobeschi.com
 */
use App;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\MessageBag;
use Jacopo\Authentication\Exceptions\PermissionException;
use Jacopo\Authentication\Exceptions\UserNotFoundException;
use Jacopo\Authentication\Validators\UserProfileValidator;
use Jacopo\Library\Exceptions\InvalidException;
use Jacopo\Library\Exceptions\JacopoExceptionsInterface;
use Jacopo\Library\Form\FormModel;

class UserProfileService
{

    /**
     * User repository
     */
    protected $r_u;
    /**
     * Profile repository
     */
    protected $r_p;
    /**
     * @var UserProfileValidator
     */
    protected $v_p;
    /**
     * @var FormModel
     */
    protected $f_p;
    /**
     * @var \Illuminate\Support\MessageBag
     */
    protected $errors;

    function __construct($v_p, $f_p = null)
    {
        // user repo
        $this->r_u = App::make('user_repository');
        // profile formModel
        $this->r_p = App::make('profile_repository');
        $this->v_p = $v_p ? $v_p : new UserProfileValidator;
        $this->f_p = $f_p ? $f_p : new FormModel($this->v_p, $this->r_p);
    }

    public function processForm($input)
    {
        $this->checkPermission($input);

        $user_profile = $this->createUserProfile($input);

        $this->updateUserPassword($input);

        return $user_profile;
    }

    /**
     * @param $input
     * @return mixed
     * @throws \Jacopo\Library\Exceptions\InvalidException
     */
    protected function createUserProfile($input)
    {
        try {
            $user_profile = $this->f_p->process($input);

            return $user_profile;
        }
        catch (JacopoExceptionsInterface $e) {
            $this->errors = $this->f_p->getErrors();
            throw new InvalidException;
        }g

        return $user_profile;
    }

    /**
     * @param $input
     * @throws \Jacopo\Authentication\Exceptions\PermissionException
     */
    protected function checkPermission($input)
    {
        $auth_helper = App::make('authentication_helper');
        if (! $auth_helper->checkProfileEditPermission($input["user_id"]))
        {
            $this->errors = new MessageBag(["model" => "Non hai i permessi di modificare questo profilo"]);
            throw new PermissionException;
        }
    }

    /**
     * @param $input
     * @throws \Jacopo\Authentication\Exceptions\UserNotFoundException
     */
    protected function updateUserPassword($input)
    {
        if (isset($input["new_password"]) && !empty($input["new_password"]))
        try {
            $this->r_u->update(
                               $input["user_id"],
                               ["password" => $input["new_password"]]
                               );
        }
        catch (ModelNotFoundException $e)
        {
            throw new UserNotFoundException;
        }
    }

    /**
     * @return \Illuminate\Support\MessageBag
     */
    public function getErrors()
    {
        return $this->errors;
    }
} 