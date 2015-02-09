<div class="row form-group">
    <div class="col-md-12">
        {{Form::label('Sorting: ')}}
    </div>
    <div class="col-md-12 margin-top-10">
        {{Form::select('', ["" => "select column", "first_name" => "First name", "last_name" => "Last name", "email" => "Email", "last_login" => "Last login", "activated" => "Active"], Input::get('order_by',''), ['class' => 'form-control form-validable', 'id' => 'order-by-select'])}}
        <span class="text-danger hidden form-error-message">The order by field is required.</span>
    </div>
    <div class="col-md-12 margin-top-10">
        {{Form::select('', ["asc" => "Ascending", "desc" => "Descending"], Input::get('ordering','asc'), ['class' =>'form-control', 'id' => 'ordering-select'])}}
    </div>
    <div class="col-md-12 margin-top-10">
        <a class="btn btn-default pull-right" id="add-ordering-filter"><i class="fa fa-plus"></i> Add</a>
    </div>
    <span id="append-sorting"></span>
    {{Form::hidden('order_by',Input::get('order_by'),["id" => "order-by" ])}}
    {{Form::hidden('ordering',Input::get('ordering'), ["id" => "ordering"])}}
</div>

@section('footer_scripts')
@parent
{{ HTML::script('packages/jacopo/laravel-authentication-acl/js/custom-ordering.js') }}
@stop