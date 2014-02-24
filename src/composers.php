<?php

/**
 * Send to the view the site name
 */
View::composer('authentication::*', function ($view){
    $view->with('app_name', Config::get('authentication::app_name') );
});

use Jacopo\Authentication\Classes\Menu\SentryMenuFactory;
/**
 * Send the menu items
 */
View::composer('authentication::layouts.*', function ($view){
    $menu_items = SentryMenuFactory::create()->getItemListAvailable();
    $view->with('menu_items', $menu_items);
});

/**
 * Create users sidebar
 */
View::composer(['authentication::user.*', 'authentication::group.*', 'authentication::permission.*'], function ($view){
    $view->with('sidebar_items', [
                                    "Lista utenti" => URL::route('users.list'),
                                    "Aggiungi utente" => URL::route('users.edit'),
                                    "Lista gruppi" => URL::route('users.groups.list'),
                                    "Aggiungi gruppo" => URL::route('users.groups.edit'),
                                    "Lista permessi" => URL::route('users.permission.list'),
                                    "Aggiungi permesso" => URL::route('users.permission.edit'),
                                 ]);
});

use Jacopo\Authentication\Helpers\FormHelper;
/**
 * Sends the permission select to the view
 */
View::composer(['authentication::user.edit','authentication::group.edit'], function ($view){
    $fh = new FormHelper();
    $values_permission = $fh->getSelectValuesPermission();
    $view->with('permission_values', $values_permission);
});
/**
 * Sends the group select to the view
 */
View::composer(['authentication::user.edit','authentication::group.edit'], function ($view){
    $fh = new FormHelper();
    $values_group = $fh->getSelectValuesGroups();
    $view->with('group_values', $values_group);
});