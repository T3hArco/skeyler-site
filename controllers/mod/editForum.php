<?php
$isJson = false;
require '_mod.php';

// check permission
validateRank(User::RANK_ADMIN);

$forumId = (int) getGet('forumId');

if (getPost('submit')) {
  $name = getPost('name');
  $description = getPost('description');
  $visibleRank = (int) getPost('visibleRank');
  $createPostRank = (int) getPost('createPostRank');
  $createThreadRank = (int) getPost('createThreadRank');
  $parentId = (int) getGet('parentId');

  if (!$forumId) {
    $forumId = Forums::createForum($parentId, $name, $description, $visibleRank, $createPostRank, $createThreadRank);
  }
  else {
    Forums::editForum($forumId, $name, $description, $visibleRank, $createPostRank, $createThreadRank);
  }
  redirect('/forums/?forumId=' . $forumId);
}

if (!$forumId) {
  $Page->header('Create New Forum');
  $forum = array(
    'name' => '',
    'description' => '',
    'visibleRank' => -1,
    'createPostRank' => 0,
    'createThreadRank' => 0,
  );
  $data['parentId'] = (int) getGet('parentId');
}
else {
  $forum = Forums::load($forumId);
  if (!$forum) {
    Notice::error('No forum found with that ID!');
    return;
  }
}

$Page->header();

$data['forum'] = $forum;
$data['isCreate'] = true;
view($data);

