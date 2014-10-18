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
            "user_edit_success" => "User edited with success.",
            "user_delete_success" => "User deleted with success.",
            "user_group_add_success" => "Group added with success.",
            "user_group_delete_success" => "Group deleted with success.",
            "user_permission_add_success" => "Permission added with success.",
            "user_profile_edit_success" => "Profile edited with success.",
            "custom_field_added" => "Field added succesfully.",
            "custom_field_removed" => "Field removed succesfully.",
            "avatar_edit_success" => "Avatar changed succesfully",
            // group
            "group_edit_success"                   => "Group edited succesfully.",
            "group_delete_success"                 => "Group deleted succesfully.",
            "group_permission_edit_success"        => "Permission edited succesfully.",
            // permission
            "permission_permission_edit_success"   => "Permission edited with success.",
            "permission_permission_delete_success" => "Permission deleted with success.",
          ],
          /*
           * User error messages
           */
          "error"   => [
            // user
            "user_group_not_found" => "Group not found.",
            "user_permission_not_found" => "Permission not found",
            "user_user_not_found" => "User not found.",
            "custom_field_not_found" => "Cannot find the custom field.",
            "cannot_upload_file" => "Cannot upload the file.",
            // group
            "group_permission_not_found" => "Permission not found.",
            // permission
          ]
        ]
];