<?php
require '../_.php';

global $User, $Config, $pageId;

$Page = new Page();
$Page->header('User Search');

$search = (string) getGet('search');
$sort = (string) getGet('sort');
$order = getGet('order') === 1 ? 'DESC' : 'ASC';

$users = array();

$users = User::searchUsers($search);

$data = array(
  'search' => $search,
  'users' => $users,
);

view($data);
