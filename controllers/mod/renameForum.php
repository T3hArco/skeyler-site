<?php
require '_mod.php';

// check permission
validateRank(User::RANK_SUPER);

if (!isset($_POST['forumId'])) {
  Notice::error('You didn\'t send a forum ID!!!!');
  exit;
}

$forumId = getPost('forumId');

$forum = Forums::load($forumId);

if (!$forum) {
  Notice::error('Could not find the thread you want to open!!!!');
  exit;
}

$name = getPost('name');

$hasDescription = isset($_POST['description']);

$description = getPost('description');

if (!$hasDescription) {
  Forums::rename($forumId, $name);
}
else {
  Forums::rename($forumId, $name, $description);
}

Notice::success('Wow, what a good name! Thanks for renaming this forum!!!');

$data = array(
  'forumId' => $forumId,
  'success' => true,
);

view($data);