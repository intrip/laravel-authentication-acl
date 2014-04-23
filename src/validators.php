<?php

Validator::extend('mail_signup', 'Jacopo\Authentication\Validators\UserSignupEmailValidator@validateEmailUnique');