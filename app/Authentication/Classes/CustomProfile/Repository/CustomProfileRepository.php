<?php  namespace LaravelAcl\Authentication\Classes\CustomProfile\Repository;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;

/**
 * Class CustomProfileRepository
 *
 * @author jacopo beschi jacopo@jacopobeschi.com
 */
class CustomProfileRepository
{
    protected $profile_id;

    protected static $profile_field = 'LaravelAcl\Authentication\Models\ProfileField';
    protected static $profile_field_type = 'LaravelAcl\Authentication\Models\ProfileFieldType';

    protected static $profile_field_model = NULL;
    protected static $profile_field_type_model = NULL;


    public function __construct($profile_id)
    {
        $config = config('cartalyst.sentry');
        if (isset($config['profile_field']) && isset($config['profile_field']['model'])) {
            self::$profile_field = $config['profile_field']['model'];
        }
        if (isset($config['profile_field_type']) && isset($config['profile_field_type']['model'])) {
            self::$profile_field_type = $config['profile_field_type']['model'];
        }
        self::$profile_field_model = new self::$profile_field;
        self::$profile_field_type_model = new self::$profile_field_type;

        $this->profile_id = is_array($profile_id) ? array_shift($profile_id) : $profile_id;
    }

    public static function getAllTypes()
    {
        return self::$profile_field_type_model->all();
    }

    public static function addNewType($description)
    {
        // firing event so it can get catched for permission handling
        Event::fire('customprofile.creating');
        $profile_field_type = self::$profile_field_type_model->create(["description" => $description]);

        return $profile_field_type;
    }

    public static function deleteType($id)
    {
        // firing event so it can get catched for permission handling
        Event::fire('customprofile.deleting');
        $success = self::$profile_field_type_model->findOrFail($id)->delete();

        return $success;
    }

    public function setField($profile_type_field_id, $field_value)
    {
        try
        {
            $profile = $this->findField($profile_type_field_id);
        }
        catch(ModelNotFoundException $e)
        {
            return $this->createNewField($profile_type_field_id, $field_value);
        }

        return $profile->fill(["value" => $field_value])->save();
    }

    /**
     * @param $profile_id
     * @param $profile_type_field_id
     * @param $field_value
     */
    protected function createNewField($profile_type_field_id, $field_value)
    {
        return self::$profile_field_model->create([
                                    "profile_id"            => $this->profile_id,
                                    "profile_field_type_id" => $profile_type_field_id,
                                    "value"                 => $field_value
                                    ]);
    }

    public function getAllTypesWithValues()
    {
        $profile_fields_with_values = [];

        $all_profile_types = $this->getAllTypes();
        foreach($all_profile_types as $profile_type)
        {
            $profile_field_with_values = new \StdClass;
            $this->setValuesFromFieldType($profile_type, $profile_field_with_values);
            $this->setValuesFromFieldValue($profile_type, $profile_field_with_values);

            $profile_fields_with_values[] = $profile_field_with_values;
        }

        return $profile_fields_with_values;
    }

    public function getAllFields()
    {
        return self::$profile_field_model->where('profile_id','=',$this->profile_id)
                ->get();
    }
    
    /**
     * @param $profile_id
     * @param $profile_type_field_id
     * @return mixed
     */
    public function findField($profile_type_field_id)
    {
        return  self::$profile_field_model->where('profile_id', '=', $this->profile_id)
                ->where('profile_field_type_id', '=', $profile_type_field_id)
                ->firstOrFail();
    }

    /**
     * @param $profile_type
     * @param $profile_field_with_values
     */
    protected function setValuesFromFieldType($profile_type, $profile_field_with_values)
    {
        $profile_field_with_values->id          = $profile_type->id;
        $profile_field_with_values->description = $profile_type->description;
    }

    /**
     * @param $profile_type
     * @param $profile_field_with_values
     */
    protected function setValuesFromFieldValue($profile_type, $profile_field_with_values)
    {
        $profile_field_value = $this->hasProfileFieldValueAssociated($profile_type) ? $this->fetchProfileValueAssociated($profile_type) : null;
        $profile_field_with_values->value = $profile_field_value;
    }

    /**
     * @param $profile_type
     * @return mixed
     */
    protected function hasProfileFieldValueAssociated($profile_type)
    {
        return $profile_type->profile_field()->whereProfileId($this->profile_id)->count();
    }

    /**
     * @param $profile_type
     * @return mixed
     */
    protected function fetchProfileValueAssociated($profile_type)
    {
        return $profile_type->profile_field()->whereProfileId($this->profile_id)->first()->value;
    }

}