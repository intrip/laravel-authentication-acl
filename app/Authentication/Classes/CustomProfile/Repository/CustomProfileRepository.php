<?php  namespace Jacopo\Authentication\Classes\CustomProfile\Repository;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Jacopo\Authentication\Models\ProfileField;
use Jacopo\Authentication\Models\ProfileFieldType;

/**
 * Class CustomProfileRepository
 *
 * @author jacopo beschi jacopo@jacopobeschi.com
 */
class CustomProfileRepository
{
    protected $profile_id;

    public function __construct($profile_id)
    {
        $this->profile_id = $profile_id;
    }

    public static function getAllTypes()
    {
        return ProfileFieldType::all();
    }

    public static function addNewType($description)
    {
        Event::fire('customprofile.creating');
        $profile_field_type = ProfileFieldType::create(["description" => $description]);

        return $profile_field_type;
    }

    public static function deleteType($id)
    {
        Event::fire('customprofile.deleting');
        $success = ProfileFieldType::findOrFail($id)->delete();

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
        return ProfileField::create([
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
        return ProfileField::where('profile_id','=',$this->profile_id)
                ->get();
    }
    
    /**
     * @param $profile_id
     * @param $profile_type_field_id
     * @return mixed
     */
    public function findField($profile_type_field_id)
    {
        return ProfileField::where('profile_id', '=', $this->profile_id)
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