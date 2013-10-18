<?php
require '../_.php';
$Page = new Page();


$forumId = (int)getGet('forumId');


if (!$forumId) {
  Notice::error('You so silly. You can\'t make a thread on the forum index. It\'s illegal!');
  exit;
}

$forum = Forums::load($forumId);

if (!$forum) {
  Notice::error('Oh no! That forum does not exist!');
  exit;
}

if ($User['rank'] < $forum['visibleRank'] || $User['rank'] < $forum['createPostRank'] || $User['rank'] < $forum['createThreadRank']) {
  Notice::error('You do not have permission to create a new thread on this forum.');
  exit;
}


$local['breadcrumbs'] = array(
  'Forums' => '/forums.php',
  $forum['name'] => '/forums.php?forumId=' . $forumId,
);

// they submitted the form

$content = getPost('content');
$title = getPost('title');

$isSubmit = isset($_POST['submit']);
if ($isSubmit) {
  $hasErrors = false;

  if (strlen($title) < 5) {
    Notice::error('Title too short. Make it at least 5 characters.');
    $hasErrors = true;
  }
  if (strlen($content) < 5) {
    Notice::error('Content too short. Make it at least 5 characters.');
    $hasErrors = true;
  }

  if (!$hasErrors) {
    $threadId = Threads::insertThread($title, $content, $forumId);
    redirect('/thread.php?threadId=' . $threadId);
  }


}

$Page->header('Create Thread');
$data['content'] = $content;
$data['title'] = $title;


view($data);