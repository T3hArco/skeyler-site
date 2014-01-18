<?php

require '../_.php';


setcookie('userId', 'butt', $now - 60 * 60 * 24 * 365);
setcookie('authKey', 'poop', $now - 60 * 60 * 24 * 365);

session_destroy();

$isLoggedIn = false;
$Page = new Page();

Notice::success('NOOO! COME BACK!');

$Page->header('Logout');


$data = array();

$data['username'] = $User['name'];

view($data);