<?php
require '_mod.php';

// check permission
validateRank(User::RANK_ADMIN);

$threadId = (int)getPost('threadId');

$thread = Threads::load($threadId);

if(!$thread) {
  Notice::error('Could not find the thread you want to open!!!!');
  exit;
}

$title = getPost('title');

Threads::rename($threadId, $title);

Notice::success('Wow, what a good name! Thanks for renaming this thread!!!');

$data = array(
  'threadId' => $threadId,
  'success' => true,
);

view($data);