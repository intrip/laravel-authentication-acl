<?php  namespace Jacopo\Authentication\Controllers; 

use Illuminate\Routing\Controller;
use View;

class DashboardController extends Controller{

    public function base()
    {
        return View::make('laravel-authentication-acl::admin.dashboard.default');
    }
} 