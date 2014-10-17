<?php

return [
        "email" => [
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
        ],

        /*
        |--------------------------------------------------------------------------
        | Flash messages
        |--------------------------------------------------------------------------
        |
        */
        "flash" => [
          /*
           * User success messages
           */
          "success" => [
            // user

            // group
            "group_edit_success" => "Group edited succesfully.",
            "group_delete_success" => "Group deleted succesfully.",
            "group_permission_edit_success" => "Permission edited succesfully.",
            // permission

          ],
          /*
           * User error messages
           */
          "error"   => [
            // user
            // group
            "group_permission_not_found" => "Permission not found",
            // permission
          ]
        ]
];