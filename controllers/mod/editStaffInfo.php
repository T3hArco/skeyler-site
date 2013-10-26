<?php
require '_mod.php';

// check permission
validateRank(User::RANK_ADMIN);

$userId = (int)getPost('userId');
$title = getPost('title');
$description = getPost('description');

if(strlen($title) == 0 && strlen($description) == 0) {
  Notice::error('Title/description cannot be blank!');
  exit;
}

if (!$userId) {
  Notice::error('Missing userId! Oh no! Could not edit.');
  exit;
}

if (strlen($title) > 0) {
  $staffInfo = Cache::load('staff');
  $description = '';
  if (isset($staffInfo[$userId], $staffInfo[$userId]['description'])) {
    $description = $staffInfo[$userId]['description'];
  }
  $staffInfo[$userId] = array(
    'title' => $title,
    'description' => $description,
  );
  $staffInfo = Cache::save('staff', $staffInfo);
  Notice::success('You edited a job title!');
}

if (strlen($description) > 0) {
  $staffInfo = Cache::load('staff');
  $title = '';
  if (isset($staffInfo[$userId], $staffInfo[$userId]['title'])) {
    $title = $staffInfo[$userId]['title'];
  }
  $staffInfo[$userId] = array(
    'title' => $title,
    'description' => $description,
  );
  $staffInfo = Cache::save('staff', $staffInfo);
  Notice::success('You edited a job description!');
}

$data = array(
  'success' => true,
);

view($data);