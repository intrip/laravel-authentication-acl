@extends('authentication::admin.layouts.base-2cols')

@section('title')
Admin area: dashboard
@stop

@section('content')
<div class="row">
  <div class="col-md-12">
      Welcome, {{$logged_user->email}}.
      You can find this page at views/packages/authentication/admin/dashboard.blade.php
  </div>
</div>
@stop