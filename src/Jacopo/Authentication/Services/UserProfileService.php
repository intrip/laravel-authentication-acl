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
    protected $user_repository;
    /**
     * Profile repository
     */
    protected $profile_repository;
    /**
     * @var UserProfileValidator
     */
    protected $validator_profile;
    /**
     * @var FormModel
     */
    protected $form_model_profile;
    /**
     * @var \Illuminate\Support\MessageBag
     */
    protected $errors;
    protected $custom_profile_field_prefix = "custom_profile_";

    function __construct($validator_profile, $form_profile = null)
    {
        // user repo
        $this->user_repository = App::make('user_repository');
        // profile formModel
        $this->profile_repository = App::make('profile_repository');
        $this->validator_profile = $validator_profile ? $validator_profile : new UserProfileValidator;
        $this->form_model_profile = $form_profile ? $form_profile : new FormModel($this->validator_profile, $this->profile_repository);
    }

    public function processForm($input)
    {
        //@todo validate the user input

        $this->checkProfileEditPermission($input);

        $this->user_repository->update($input['user_id'], array_only($input, ['password']));

        $user_profile = $this->updateUserProfile($input);

        $this->saveCustomProfileFields($input, $user_profile);

        return $user_profile;
    }

    /**
     * @param $input
     * @return mixed
     * @throws \Jacopo\Library\Exceptions\InvalidException
     */
    protected function updateUserProfile($input)
    {
        try {
            $user_profile = $this->form_model_profile->process($input);

            return $user_profile;
        }
        catch (JacopoExceptionsInterface $e) {
            $this->errors = $this->form_model_profile->getErrors();
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