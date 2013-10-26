<?php
require '../_.php';
$Page = new Page();

$users = User::getStaff();

$Page->header();

//$staff = array(
//  '12' => array('header' => 'asdf', 'description' => 'hi'),
//
//);
//Cache::save('staff', $staff);

$staffInfo = Cache::load('staff');

$data['users'] = $users;
$data['staffInfo'] = $staffInfo;
view($data);