<?php  namespace Jacopo\Authentication\Tests;

use Jacopo\Authentication\Classes\CustomProfile\Repository\CustomProfileRepository;
use Jacopo\Authentication\Models\ProfileField;
use Jacopo\Authentication\Models\ProfileFieldType;

/**
 * Test CustomProfileTest
 *
 * @author jacopo beschi jacopo@jacopobeschi.com
 */
class CustomProfileRepositoryTest extends DbTestCase {

    protected $custom_profile;
    protected $profile_id;

    public function setUp()
    {
        parent::setUp();
        $this->profile_id = 1;
        $this->custom_profile = new CustomProfileRepository($this->profile_id);
    }

    /**
     * @test
     **/
    public function canShowAllCustomFieldsType()
    {
        $profile_type = ProfileFieldType::create(["description" => "invoice number"]);

        $profile_types = $this->custom_profile->getAllTypes();

        $this->objectHasAllArrayAttributes($profile_type->toArray(), $profile_types->first());
    }

    /**
     * @test
     **/
    public function canAddCustomFieldType()
    {
        $description   = "custom field type";

        $this->custom_profile->addNewType($description);

        $profile_types = $this->custom_profile->getAllTypes();
        $this->assertCount(1,$profile_types);
    }

    /**
     * @test
     **/
    public function canAddCustomField()
    {
        $field_value = "value";
        $profile_type_field_id = 1;

        $this->custom_profile->setField($profile_type_field_id, $field_value);

        $created_profile = ProfileField::first();
        $this->assertEquals($field_value, $created_profile->value);
    }

    /**
     * @test
     **/
    public function canUpdateCustomField()
    {
        $field_value = "value";
        $profile_type_field_id = 1;
        $this->createCustomField($profile_type_field_id, $field_value);

        $new_value = "value2";
        $this->custom_profile->setField($profile_type_field_id, $new_value);

        $total_fields = 1;
        $this->assertEquals($total_fields, ProfileField::all()->count());
        $this->assertEquals($new_value, ProfileField::first()->value);
    }

    /**
     * @test
     **/
    public function canGetAllTypesWithTheirAssociatedValueIfExists()
    {
        $type_description1   = "custom field type with value";
        $profile_type = $this->custom_profile->addNewType($type_description1);
        $value = "value";
        $this->custom_profile->setField($profile_type->id, $value);

        $type_description2   = "custom field type without value";
        $this->custom_profile->addNewType($type_description2);

        $created_fields = $this->custom_profile->getAllTypesWithValues();

        $this->assertNotEmpty($created_fields);
        $this->assertCount(2, $created_fields);
        $this->assertEquals($created_fields[0]->value, $value);
        $this->assertNull($created_fields[1]->value, $value);
    }

    /**
     * @test
     **/
    public function canGetAllFieldsOfAProfile()
    {
        $profile_type_field_id_1 = 1;
        $value_1 = "value1";
        $this->createCustomField($profile_type_field_id_1, $value_1);
        $profile_type_field_id_2 = 2;
        $value_2 = "value2";
        $this->createCustomField($profile_type_field_id_2, $value_2);

        $fields = $this->custom_profile->getAllFields();
        $total_fields = 2;

        $this->assertEquals($total_fields, $fields->count());
        $this->assertEquals($value_1, $fields[0]->value);
        $this->assertEquals($value_2, $fields[1]->value);
    }

    /**
     * @param $profile_id
     * @param $profile_type_field_id
     * @param $field_value
     */
    protected function createCustomField($profile_type_field_id, $field_value)
    {
        ProfileField::create([
                             "profile_id"            => $this->profile_id,
                             "profile_field_type_id" => $profile_type_field_id,
                             "value"                 => $field_value
                             ]);
    }
}
 