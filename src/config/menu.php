<?php

return [
    "list" => [
                [
                    "name" => "Users",
                    "route" => "users",
                    "link" => URL::route('users.list'),
                    "permissions" => ["_superadmin", "_user-editor"]
                ],
                [
                    "name" => "Groups",
                    "route" => "groups",
                    "link" => URL::route('groups.list'),
                    "permissions" => ["_superadmin", "_group-editor"]
                ],
                [
                    "name" => "Permission",
                    "route" => "permission",
                    "link" => URL::route('permission.list'),
                    "permissions" => ["_superadmin", "_permission-editor"]
                ],
      ]
];