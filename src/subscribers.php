<?php
/*
|--------------------------------------------------------------------------
| Editable subscriber
|--------------------------------------------------------------------------
|
| Check if the current record is editable
|
*/
use Jacopo\Authentication\Events\EditableSubscriber;
Event::subscribe(new EditableSubscriber());
