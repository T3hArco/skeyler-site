<?php

require '../_.php';
$Page = new Page();

setcookie('userId', 'butt', $now - 60 * 60 * 24 * 365);
setcookie('authKey', 'poop', $now - 60 * 60 * 24 * 365);

session_destroy();

Notice::success('NOOO! COME BACK!');

$Page->header('Logout');


$data = array();

$data['username'] = $User['name'];

view($data);