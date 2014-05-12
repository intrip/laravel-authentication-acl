<?php  namespace Jacopo\Authentication\Classes\CustomProfile\Repository; 
/**
 * Class ProfileFieldTypeRepository
 *
 * @author jacopo beschi jacopo@jacopobeschi.com
 */
use Jacopo\Authentication\Models\ProfileFieldType;

class ProfileFieldTypeRepository 
{
    public static function getAllTypes()
    {
        return ProfileFieldType::all();
    }

    public static function addNewType($description)
    {
        return ProfileFieldType::create(["description" => $description]);
    }

    public static function deleteType($id)
    {
        return ProfileFieldType::findOrFail($id)->delete();
    }
} 