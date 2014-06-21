{{-- add permission --}}
{{Form::open(["route" => "users.edit.permission","role"=>"form", 'class' => 'form-add-perm'])}}
<div class="form-group">
    <div class="input-group">
        <span class="input-group-addon form-button button-add-perm"><span class="glyphicon glyphicon-plus-sign add-input"></span></span>
        {{Form::select('permissions', $permission_values, '', ["class"=>"form-control permission-select"])}}
    </div>
    <span class="text-danger">{{$errors->first('permissions')}}</span>
    {{Form::hidden('id', $user->id)}}
    {{-- add permission operation --}}
    {{Form::hidden('operation', 1)}}
</div>
@if(! $user->exists)
<div class="form-group">
    <span class="text-danger"><h5>You need to create the user first.</span>
</div>
@endif
{{Form::close()}}

{{-- remove permission --}}
@if( $presenter->permissions )
@foreach($presenter->permissions_obj as $permission)
{{Form::open(["route" => "users.edit.permission", "name" => $permission->permission, "role"=>"form"])}}
<div class="form-group">
    <div class="input-group">
        <span class="input-group-addon form-button button-del-perm" name="{{$permission->permission}}"><span class="glyphicon glyphicon-minus-sign add-input"></span></span>
        {{Form::text('permission_desc', $permission->description, ['class' => 'form-control', 'readonly' => 'readonly'])}}
        {{Form::hidden('permissions', $permission->permission)}}
        {{Form::hidden('id', $user->id)}}
        {{-- add permission operation --}}
        {{Form::hidden('operation', 0)}}
    </div>
</div>
{{Form::close()}}
@endforeach
@elseif($user->exists)
<span class="text-warning"><h5>There is no permission associated to the user.</h5></span>
@endif

@section('footer_scripts')
@parent
<script>
    $(".button-add-perm").click(function () {
        <?php if($user->exists): ?>
        $('.form-add-perm').submit();
        <?php endif; ?>
    });
    $(".button-del-perm").click(function () {
        // submit the form with the same name
        name = $(this).attr('name');
        $('form[name='+name+']').submit();
    });
</script>
@stop