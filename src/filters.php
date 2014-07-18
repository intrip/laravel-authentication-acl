<?php
/*
|--------------------------------------------------------------------------
| Guest Filter
|--------------------------------------------------------------------------
|
| Check that the current user is logged and active
|
*/

Route::filter('logged', function()
{
    if (! App::make('authenticator')->check()) return Redirect::to('/admin/login');
});

/*
|--------------------------------------------------------------------------
| Permission Filter
|--------------------------------------------------------------------------
|
| Check that the current user is logged in and has a the permission corresponding to the config menu file
|
*/
use Jacopo\Authentication\Helpers\FileRouteHelper;

Route::filter('can_see', function()
{
    $route_helper = new FileRouteHelper;
    if( ! $route_helper->hasPermForRoute(Route::currentRouteName())) App::abort('401');
});