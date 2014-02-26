{{Form::open(["route" => "users.groups.edit.permission","role"=>"form", 'class' => 'form-add-perm'])}}
<div class="form-group">
    <div class="input-group">
        <span class="input-group-addon form-button button-add-perm"><span class="glyphicon glyphicon-plus-sign add-input"></span></span>
        {{Form::select('permissions', $permission_values, '', ["class"=>"form-control permission-select"])}}
    </div>
    <span class="text-danger">{{$errors->first('permissions')}}</span>
    {{Form::hidden('id', $group->id)}}
    {{-- add permission operation --}}
    {{Form::hidden('operation', 1)}}
    </div>
    <div class="form-group">
        @if(! $group->exists)
        <h5 style="color:gray">Per associare permessi bisogna prima creare il gruppo.</h5>
        @endif
    </div>
{{Form::close()}}

@if( $presenter->permissions )
@foreach($presenter->permissions_obj as $permission)
    {{Form::open(["route" => "users.groups.edit.permission", "role"=>"form", 'class' => 'form-del-perm'])}}
    <div class="form-group">
        <div class="input-group">
            <span class="input-group-addon form-button button-del-perm"><span class="glyphicon glyphicon-minus-sign add-input"></span></span>
                       {{Form::text('permission_desc', $permission->description, ['class' => 'form-control', 'readonly' => 'readonly'])}}
         {{Form::hidden('permissions', $permission->permission)}}
            {{Form::hidden('id', $group->id)}}
            {{-- add permission operation --}}
            {{Form::hidden('operation', 0)}}
        </div>
    </div>
    {{Form::close()}}
@endforeach
@else
<h5>Non ci sono permessi associati al gruppo.</h5>
@endif

@section('footer_scripts')
@parent
<script>
    $(".button-add-perm").click( function(){
        <?php if($group->exists): ?>
        $('.form-add-perm').submit();
        <?php endif; ?>
    });
    $(".button-del-perm").click( function(){
        $('.form-del-perm').submit();
    });
</script>
@stop