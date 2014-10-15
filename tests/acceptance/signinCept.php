<?php 
$I = new AcceptanceTester($scenario);
$I->wantTo('signup a new user');
$I->amOnPage('/user/signup');
$I->see('Please sign up for Authenticator');
$I->fillField('first_name','');

