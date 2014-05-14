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

        return $profile->update(["value" => $field_value]);
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
        return DB::table('profile_field_type')
                ->leftJoin('profile_field','profile_field_type.id','=','profile_field.profile_field_type_id')
                ->where('profile_field.profile_id','=',$this->profile_id)
                ->orWhere('profile_field.profile_id','=',null)
                ->select(['profile_field_type.*','profile_field.value'])
                ->get();
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

}