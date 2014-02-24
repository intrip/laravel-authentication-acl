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
use Jacopo\Authentication\Helpers\SentryAuthenticationHelper as AuthHelper;

Route::filter('can_see', function()
{
    $helper = new FileRouteHelper;
    echo "<br/><br/><br/><br/>";
    if( ! (AuthHelper::hasPermission( $helper->getPermFromCurrentRoute() ) ) ) App::abort('401');
});