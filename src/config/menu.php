<?php

return [
        [
            "name" => "Users",
            "route" => "users",
            "link" => URL::route('users.list'),
            "permissions" => ["_admin"]
        ],
];