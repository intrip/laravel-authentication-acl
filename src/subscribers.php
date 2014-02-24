<?php
/*
|--------------------------------------------------------------------------
| Editable subscriber
|--------------------------------------------------------------------------
|
| Check if the current record is editable
|
*/
Event::subscribe(new \Jacopo\Authentication\Events\EditableSubscriber());
