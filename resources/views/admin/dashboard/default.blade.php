@extends('laravel-authentication-acl::admin.layouts.base')

@section('title')
Admin: Dashboard
@stop
@section('content')
@push('css')
<link rel="stylesheet" href="{{ asset('amcharts/amcharts/plugins/export/export.css') }}" type="text/css" media="all" />
@endpush('css')
@push('js')
<script src="{{ asset('amcharts/amcharts/amcharts.js') }}"></script>
<script src="{{ asset('amcharts/amcharts/serial.js') }}"></script>
<script src="{{ asset('amcharts/amcharts/plugins/export/export.min.js') }}"></script>
<script src="{{ asset('amcharts/amcharts/themes/light.js') }}"></script>
<script>
var chart = AmCharts.makeChart("chartdiv", {
    "theme": "light",
    "type": "serial",
    "dataProvider": {!! collect($data->analytics)->toJson() !!},
    "valueAxes": [{
        "position": "left",
        "title": "Impression (Search with your deal on the result)"
    }],
    "graphs": [{
        "balloonText": "[[category]]: <b>[[value]]</b>",
        "fillColorsField": "color",
        "fillAlphas": 1,
        "lineAlpha": 0.1,
        "type": "column",
        "valueField": "count"
    }],
    "depth3D": 20,
	"angle": 30,
    "chartCursor": {
        "categoryBalloonEnabled": false,
        "cursorAlpha": 0,
        "zoomable": false
    },
    "categoryField": "month",
    "categoryAxis": {
        "gridPosition": "start",
        "labelRotation": 90
    },
    "export": {
    	"enabled": true
     }
});
</script>
@endpush
<div class="row">
    <div class="top_tiles text-center" style="margin: 1px 0px; padding: 1px 0px;">
        <div class='col-sm-6 panel panel-primary'>
            <div class="panel-heading text-left">Users</div>
            <div class="panel-body">
                <div class="col-sm-3 col-xs-6 tile">
                <span>Total users</span>
                <h2><i class="fa fa-users"></i> {!! $registered !!}</h2>
                </div>

                <div class="col-sm-3 col-xs-6 tile">
                <span>Active users</span>
                <h2><i class="fa fa fa-unlock-alt"></i> {!! $active !!}</h2>
                </div>

                <div class="col-sm-3 col-xs-6 tile">
                <span>Pending users</span>
                <h2><i class="fa fa fa-lock"></i> {!! $pending !!}</h2>
                </div>

                <div class="col-sm-3 col-xs-6 tile">
                <span>Banned users</span>
                <h2><i class="fa fa fa-ban"></i> {!! $banned !!}</h2>
                </div>
            </div>
        </div>
        <div class='col-sm-6 panel panel-info'>
            <div class="panel-heading text-left">Vendors</div>
            <div class="panel-body">
                <div class="col-sm-3 col-xs-6 tile">
                <span>Total</span>
                @php
                    $private_count = isset($data->vendor->private)?$data->vendor->private:'0';
                @endphp
                <h2><i class="fa fa-bank"></i> {{ isset($data->vendor->public)?$data->vendor->public + $private_count:'0' }}</h2>
                </div>

                <div class="col-sm-3 col-xs-6 tile">
                <span>Active</span>
                <h2><i class="fa fa fa-unlock-alt"></i> {{ isset($data->vendor->approved)?$data->vendor->approved:'0' }}</h2>
                </div>

                <div class="col-sm-3 col-xs-6 tile">
                <span>Public</span>
                <h2><i class="fa fa fa-eye"></i> {{ isset($data->vendor->public)?$data->vendor->public:'0' }}</h2>
                </div>
                
                <div class="col-sm-3 col-xs-6 tile">
                <span>Private</span>
                <h2><i class="fa fa fa-eye-slash"></i> {{ $private_count }}</h2>
                </div>
            </div>
        </div>
        <div class='col-sm-12 panel panel-success'>
            <div class="panel-heading text-left">Business Summary</div>
            <div class="panel-body">
                <div class="col-sm-3 col-xs-6 tile">
                <span>Shipments</span>
                <h2><i class="fa fa-building"></i> {{ isset($data->business->shipments)?$data->business->shipments:'0' }}</h2>
                </div>

                <div class="col-sm-3 col-xs-6 tile">
                <span>Zones</span>
                <h2><i class="fa fa fa-map-marker"></i> {{ isset($data->business->zones)?$data->business->zones:'0' }}</h2>
                </div>

                <div class="col-sm-3 col-xs-6 tile">
                <span>Pricings</span>
                <h2><i class="fa fa fa-money"></i> {{ isset($data->business->pricings)?$data->business->pricings:'0' }}</h2>
                </div>
                
                <div class="col-sm-3 col-xs-6 tile">
                <span>Routes</span>
                <h2><i class="fa fa fa-plane"></i> {{ isset($data->business->routes)?$data->business->routes:'0' }}</h2>
                </div>
            </div>
        </div>
        <div class='col-sm-12 hidden-xs visible-sm visible-md visible-lg'>
            <div class="row">
                <div class='col-sm-12 panel panel-warning'>
                    <div class="panel-body">
                        <div class="chartdiv" id='chartdiv'>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop