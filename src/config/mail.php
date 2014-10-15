<?php

return [
  /*
  |--------------------------------------------------------------------------
  | Email subject
  |--------------------------------------------------------------------------
  |
  | Here you can change the subject of each email sent in the system
  |
  */

  /*
   * User registration request
   */
  "user_registration_request_subject"     => "Registration request to: " . Config::get('laravel-authentication-acl::app_name'),
  /*
   * User activation
   */
  "user_registraction_activation_subject" => "Your user is activated on: " . Config::get('laravel-authentication-acl::app_name'),
  /*
   * User password recovery
   */
  "user_password_recovery_subject"        => "Password recovery request",
];