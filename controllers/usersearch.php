<?php
require '../_.php';

global $User, $Config, $pageId;

$Page = new Page();
$Page->header('User Search');

$search = getPost('search');

$users = array();

if (strlen($search) > 0) {
  $users = User::searchUsers($search);
}

$data = array(
  'search' => $search,
  'users' => $users,
);

view($data);
