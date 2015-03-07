<?php
/*
|--------------------------------------------------------------------------
| Editable subscriber
|--------------------------------------------------------------------------
|
| Check if the current record is editable
|
*/
use LaravelAcl\Authentication\Events\EditableSubscriber;
Event::subscribe(new EditableSubscriber());
/*
|--------------------------------------------------------------------------
| Profile type permissions subscriber
|--------------------------------------------------------------------------
|
| Check if the current use can edit the Profile permission types
|
*/
use LaravelAcl\Authentication\Classes\CustomProfile\Events\ProfilePermissionSubscriber;
Event::subscribe(new ProfilePermissionSubscriber());
