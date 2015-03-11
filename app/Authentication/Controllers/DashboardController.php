<?php  namespace LaravelAcl\Authentication\Controllers;

use View;

class DashboardController extends Controller{

    public function base()
    {
        return View::make('laravel-authentication-acl::admin.dashboard.default');
    }
} 