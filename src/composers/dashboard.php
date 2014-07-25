<?php
use Jacopo\Authentication\Classes\Statistics\UserStatistics;
/**
 * Dashboard information
 */
View::composer(['laravel-authentication-acl::admin.dashboard.*'], function($view){
    $user_statistics = new UserStatistics();
    $registered = $user_statistics->getRegisteredUserNumber();
    $active = $user_statistics->getActiveUserNumber();
    $pending = $user_statistics->getPendingUserNumber();
    $banned = $user_statistics->getBannedUserNumber();

    $view->with(['registered' => $registered,"active" => $active,"pending" => $pending,"banned" => $banned]);
});