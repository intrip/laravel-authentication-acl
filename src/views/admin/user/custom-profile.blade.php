<h4><i class="fa fa-magic"></i> Custom fields:</h4>

{{-- add fields --}}
{{Form::open(["action" => "Jacopo\Authentication\Controllers\UserController@addCustomFieldType", 'class' => 'form-add-profile-field', 'role' => 'form'])}}
<div class="form-group">
    <div class="input-group">
        <span class="input-group-addon form-button button-add-profile-field"><span class="glyphicon glyphicon-plus-sign add-input"></span></span>
        {{Form::text('description','',['class' =>'form-control','placeholder' => 'Custom field name'])}}
        {{Form::hidden('user_id',$user_profile->user_id)}}
    </div>
</div>
{{Form::close()}}

{{-- delete fields --}}
@foreach($custom_profile->getAllTypesWithValues() as $profile_data)
{{Form::open(["action" => "Jacopo\Authentication\Controllers\UserController@deleteCustomFieldType", 'name' => $profile_data->id, 'role' => 'form'])}}
<div class="form-group">
    <div class="input-group">
        <span class="input-group-addon form-button button-del-profile-field" name="{{$profile_data->id}}"><span
                    class="glyphicon glyphicon-minus-sign add-input"></span></span>
        {{Form::text('profile_description', $profile_data->description, ['class' => 'form-control', 'readonly' => 'readonly'])}}
        {{Form::hidden('id', $profile_data->id)}}
        {{Form::hidden('user_id',$user_profile->user_id)}}
    </div>
</div>
{{Form::close()}}
@endforeach

@section('footer_scripts')
@parent
<script>
    $(".button-add-profile-field").click(function () {
        $('.form-add-profile-field').submit();
    });
    $(".button-del-profile-field").click(function () {
        if (!confirm('Are you sure to delete this field?')) return;

        // submit the form with the same name
        name = $(this).attr('name');
        $('form[name=' + name + ']').submit();
    });
</script>
@stop