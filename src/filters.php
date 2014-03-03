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
    if (! Sentry::check()) return Redirect::to('/user/login');
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
    $helper = new FileRouteHelper;
    $auth_helper = App::make('authentication_helper');
    $perm = $helper->getPermFromCurrentRoute();

    if( $perm && (! ($auth_helper->hasPermission( $perm ))) ) App::abort('401');
});