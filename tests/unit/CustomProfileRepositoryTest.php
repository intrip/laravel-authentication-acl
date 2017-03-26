<?php  namespace LaravelAcl\Authentication\Tests\Unit;

use Event, App;
use LaravelAcl\Authentication\Classes\CustomProfile\Repository\CustomProfileRepository;
use LaravelAcl\Authentication\Models\ProfileField;
use LaravelAcl\Authentication\Models\ProfileFieldType;
use LaravelAcl\Authentication\Tests\Unit\Traits\UserFactory;

/**
 * Test CustomProfileTest
 *
 * @author jacopo beschi jacopo@jacopobeschi.com
 */
class CustomProfileRepositoryTest extends DbTestCase {
    use UserFactory;

    protected $user_1;
    protected $profile_1;
    protected $custom_profile_1;
    protected $user_2;
    protected $profile_2;
    protected $custom_profile_2;
    protected $profile_repository;

    public function setUp()
    {
        parent::setUp();

        $this->profile_repository = App::make('profile_repository');
        $this->initializeUserHasher();

        $users = $this->times(2)->make('LaravelAcl\Authentication\Models\User',(function (){return $this->getUserStub();}));
        $this->user_1=  $users[0];
        $this->user_2=  $users[1];
        $this->profile_1 = $this->profile_repository->attachEmptyProfile($this->user_1);
        $this->profile_2 = $this->profile_repository->attachEmptyProfile($this->user_2);
        $this->custom_profile_1 = new CustomProfileRepository($this->profile_1->id);
        $this->custom_profile_2 = new CustomProfileRepository($this->profile_2->id);
        $this->withoutEvents();
    }

    /**
     * @test
     **/
    public function canShowAllCustomFieldsType()
    {
        $profile_type = ProfileFieldType::create(["description" => "invoice number"]);

        $profile_types = $this->custom_profile_1->getAllTypes();

        $this->objectHasAllArrayAttributes($profile_type->toArray(), $profile_types->first());
    }

    /**
     * @test
     **/
    public function canAddCustomFieldType()
    {
        $description   = "custom field type";

        $this->custom_profile_1->addNewType($description);

        $profile_types = $this->custom_profile_1->getAllTypes();
        $this->assertCount(1,$profile_types);
    }

    /**
     * @test
     **/
    public function canAddCustomField()
    {
        $field_value = "value";
        $field_description = "junk data";
        $profile_type_field = $this->custom_profile_1->addNewType($field_description);

        $this->custom_profile_1->setField($profile_type_field->id, $field_value);

        $created_profile = ProfileField::first();
        $this->assertEquals($field_value, $created_profile->value);
    }

    /**
     * @test
     **/
    public function canDeleteCustomFieldTypeAndValues()
    {
        $description  = "description";
        $profile_type = $this->custom_profile_1->addNewType($description);
        $profile_type_field_id = $profile_type->id;
        ProfileField::create([
                             "profile_id"            => $this->profile_1->id,
                             "profile_field_type_id" => $profile_type_field_id,
                             "value"                 => "value1"
                             ]);

        ProfileField::create([
                             "profile_id"            => $this->profile_2->id,
                             "profile_field_type_id" => $profile_type_field_id,
                             "value"                 => "value2"
                             ]);

        $this->custom_profile_1->deleteType($profile_type_field_id);

        $this->assertTrue($this->custom_profile_1->getAllTypes()->isEmpty());
        $fields_found = $this->custom_profile_1->getAllTypesWithValues();
        $this->assertCount(0,$fields_found);
    }

    /**
     * @test
     * @expectedException \Illuminate\Database\Eloquent\ModelNotFoundException
     **/
    public function throwsExceptionOnDeleteTypeIfNotExists()
    {
        $invalid_id = 99999;
        $this->custom_profile_1->deleteType($invalid_id);
    }

    /**
     * @test
     **/
    public function canUpdateCustomField()
    {
        $field_value = "value1";
        $profile_type = $this->custom_profile_1->addNewType("desc1");
        $this->createCustomField($profile_type->id, $field_value);

        $new_value = "value2";
        $this->custom_profile_1->setField($profile_type->id, $new_value);

        $total_fields = 1;
        $this->assertEquals($total_fields, ProfileField::all()->count());
        $this->assertEquals($new_value, ProfileField::first()->value);
    }
    
    /**
     * @test
     **/
    public function updateOnlyTheUserProfileCustomField()
    {
        $profile_field_type = $this->custom_profile_1->addNewType("junk data");
        $field_value_1 = "value1";
        $field_value_2 = "value2";

        $this->custom_profile_1->setField($profile_field_type->id, $field_value_1);
        $this->custom_profile_2->setField($profile_field_type->id, $field_value_2);

        $profile_fields = ProfileField::get();
        $this->assertEquals($field_value_1, $profile_fields[0]->value);
        $this->assertEquals($field_value_2, $profile_fields[1]->value);
    }

    /**
     * @test
     **/
    public function canGetAllTypesWithTheirAssociatedValueIfExists()
    {
        $profile_type = $this->createProfilesWithAndWithoutValuesAssociated();
        $value1 = "value";
        $this->custom_profile_1->setField($profile_type->id, $value1);
        $value2 = "value2";
        $this->custom_profile_2->setField($profile_type->id, $value2);

        $created_fields = $this->custom_profile_1->getAllTypesWithValues();

        $this->assertCount(2, $created_fields);
        $this->assertEquals($created_fields[0]->value, $value1);
        $this->assertNull($created_fields[1]->value);
        $this->assertEquals(1, $created_fields[0]->id);
        $this->assertEquals(2, $created_fields[1]->id);

        $created_fields_2 = $this->custom_profile_2->getAllTypesWithValues();

        $this->assertCount(2, $created_fields_2);
        $this->assertEquals($created_fields_2[0]->value, $value2);
        $this->assertNull($created_fields_2[1]->value);
        $this->assertEquals(1, $created_fields_2[0]->id);
        $this->assertEquals(2, $created_fields_2[1]->id);
    }

    /**
     * @return mixed
     */
    protected function createProfilesWithAndWithoutValuesAssociated()
    {
        $type_description1 = "custom field type with value";
        $profile_type      = $this->custom_profile_1->addNewType($type_description1);
        $type_description2 = "custom field type without value";
        $this->custom_profile_1->addNewType($type_description2);
        return $profile_type;
    }

    /**
     * @test
     **/
    public function canGetAllFieldsOfAProfile()
    {
        $profile_type_field_1 = $this->custom_profile_1->addNewType("junk data1");
        $profile_type_field_2 = $this->custom_profile_1->addNewType("junk data2");

        $value_1 = "value1";
        $this->createCustomField($profile_type_field_1->id, $value_1);
        $value_2 = "value2";
        $this->createCustomField($profile_type_field_2->id, $value_2);

        $fields = $this->custom_profile_1->getAllFields();
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
                             "profile_id"            => $this->profile_1->id,
                             "profile_field_type_id" => $profile_type_field_id,
                             "value"                 => $field_value
                             ]);
    }
}
 
