{{-- add group --}}
{{Form::open(["action" => "Jacopo\Authentication\Controllers\UserController@addGroup", 'class' => 'form-add-group', 'role' => 'form'])}}
<div class="form-group">
    <div class="input-group">
        <span class="input-group-addon form-button button-add-group"><span class="glyphicon glyphicon-plus-sign add-input"></span></span>
        {{Form::select('group_id', $group_values, '', ["class"=>"form-control"])}}
        {{Form::hidden('id', $user->id)}}
    </div>
    <span class="text-danger">{{$errors->first('name')}}</span>
</div>
{{Form::hidden('id', $user->id)}}
@if(! $user->exists)
<div class="form-group">
    <h5 style="color:gray">You need to create the user first.</h5>
</div>
@endif
{{Form::close()}}

{{-- delete group --}}
@if( ! $user->groups->isEmpty() )
@foreach($user->groups as $group)
    {{Form::open(["action" => "Jacopo\Authentication\Controllers\UserController@deleteGroup", "role"=>"form", 'class' => 'form-del-group'])}}
    <div class="form-group">
        <div class="input-group">
            <span class="input-group-addon form-button button-del-group"><span class="glyphicon glyphicon-minus-sign add-input"></span></span>
            {{Form::text('group_name', $group->name, ['class' => 'form-control', 'readonly' => 'readonly'])}}
            {{Form::hidden('id', $user->id)}}
            {{Form::hidden('group_id', $group->id)}}
        </div>
    </div>
    {{Form::close()}}
@endforeach
@elseif($user->exists)
    <h5>There is no groups associated to the user.</h5>
@endif

@section('footer_scripts')
@parent
<script>
    $(".button-add-group").click( function(){
        <?php if($user->exists): ?>
        $('.form-add-group').submit();
        <?php endif; ?>
    });
    $(".button-del-group").click( function(){
        $('.form-del-group').submit();
    });
</script>
@stop