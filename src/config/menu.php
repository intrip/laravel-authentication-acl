<?php

return [
        [
            "name" => "Users",
            "route" => "users",
            "link" => URL::route('users.list'),
            "permissions" => []
        ],
        [
            "name" => "Groups",
            "route" => "groups",
            "link" => URL::route('groups.list'),
            "permissions" => []
        ],
        [
            "name" => "Permission",
            "route" => "permission",
            "link" => URL::route('permission.list'),
            "permissions" => []
        ],
];