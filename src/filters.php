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

/*
 * Check that the user has one of the permission given
 */
Route::filter('has_perm', function(){
    $permissions = array_slice(func_get_args(),2);

    $authentication_helper = App::make('authentication_helper');
    if(! $authentication_helper->hasPermission($permissions)) App::abort('401');
});