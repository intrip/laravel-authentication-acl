<?php  namespace Jacopo\Authentication\Tests;

use Jacopo\Authentication\Classes\CustomProfile\Repository\ProfileFieldTypeRepository;
use Jacopo\Authentication\Models\ProfileField;
use Jacopo\Authentication\Models\ProfileFieldType;

/**
 * Test ProfileFieldTypeRepository
 *
 * @author jacopo beschi jacopo@jacopobeschi.com
 */
class ProfileFieldTypeRepositoryTest extends DbTestCase {

    public function setUp()
    {
        parent::setUp();
        $this->profile_type_repository = new ProfileFieldTypeRepository();
    }

    /**
     * @test
     **/
        public function canAddCustomFieldType()
        {
            $description   = "custom field type";

            $this->profile_type_repository->addNewType($description);

            $profile_types = $this->profile_type_repository->getAllTypes();
            $this->assertCount(1,$profile_types);
        }

    /**
     * @test
     **/
        public function canShowAllCustomFieldsType()
        {
            $profile_type = ProfileFieldType::create(["description" => "invoice number"]);

            $profile_types = $this->profile_type_repository->getAllTypes();

            $this->objectHasAllArrayAttributes($profile_type->toArray(), $profile_types->first());
        }


    /**
     * @test
     **/
        public function canDeleteCustomFieldTypeAndValues()
        {
//            //@todo refactor creation e add check for exception findOrFail
            $description  = "description";
            $profile_type = $this->profile_type_repository->addNewType($description);
            $profile_type_field_id = $profile_type->id;
            $profile_1 = 2;
            ProfileField::create([
                                 "profile_id"            => $profile_1,
                                 "profile_field_type_id" => $profile_type_field_id,
                                 "value"                 => "value1"
                                 ]);

            $profile_2 = 1;
            ProfileField::create([
                                 "profile_id"            => $profile_2,
                                 "profile_field_type_id" => $profile_type_field_id,
                                 "value"                 => "value2"
                                 ]);

            $this->custom_profile->deleteType($profile_type_field_id);

            $fields_found = $this->custom_profile->getAllTypesWithValues();
            $this->assertCount(0,$fields_found);
        }

}
 