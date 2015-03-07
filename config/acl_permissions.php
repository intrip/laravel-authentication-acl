<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Edit profile permission
    |--------------------------------------------------------------------------
    |
    | List of 'permission name' needed to edit other user's profile.
    | If the logged user as one or more of this permissions associated he can
    | edit other user's profile.
    |
    */
    "edit_profile" => ["_superadmin", "_user-editor"],
    /*
    |--------------------------------------------------------------------------
    | Edit custom profile type permission
    |--------------------------------------------------------------------------
    |
    | List of 'permission name' needed to edit the custom profile types.
    |
    */
    "edit_custom_profile" => ["_superadmin", "_profile-editor"]
];