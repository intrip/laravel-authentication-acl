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
    protected $custom_profile_field_prefix = "custom_profile_";

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
        $this->checkProfileEditPermission($input);

        $user_profile = $this->createUserProfile($input);

        $this->saveCustomProfileFields($input, $user_profile);

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
        }

        return $user_profile;
    }

    /**
     * @param $input
     * @throws \Jacopo\Authentication\Exceptions\PermissionException
     */
    protected function checkProfileEditPermission($input)
    {
        $auth_helper = App::make('authentication_helper');
        if (! $auth_helper->checkProfileEditPermission($input["user_id"]))
        {
            $this->errors = new MessageBag(["model" => "You don't have the permission to edit other user profiles."]);
            throw new PermissionException;
        }
    }

    /**
     * @return \Illuminate\Support\MessageBag
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @param $input
     * @param $user_profile
     */
    protected function saveCustomProfileFields($input, $user_profile)
    {
        $custom_profile_repository = App::make('custom_profile_repository', $user_profile->id);
        foreach($input as $input_key => $value)
        {
            if(($profile_field_type_id_position = $this->isCustomFieldKey($input_key)) !== false)
            {
                $profile_field_type_id = $this->extractCustomFieldId($input_key,
                                                                     $profile_field_type_id_position);
                $custom_profile_repository->setField($profile_field_type_id, $value);
            }
        }
    }

    /**
     * @param $input_key
     * @return int
     */
    protected function isCustomFieldKey($input_key)
    {
        return strpos($input_key, $this->custom_profile_field_prefix);
    }

    /**
     * @param $input_key
     * @param $profile_field_type_id_position
     * @return string
     */
    protected function extractCustomFieldId($input_key, $profile_field_type_id_position)
    {
        return substr($input_key, $profile_field_type_id_position + strlen($this->custom_profile_field_prefix));
    }
} 