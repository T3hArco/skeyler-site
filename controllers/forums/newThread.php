<?php
require '_forums.php';
$Page = new Page('forums');


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
  'Forums' => '/forums/',
  $forum['name'] => '/forums/?forumId=' . $forumId,
);

// they submitted the form

$content = getPost('content');
$title = getPost('title');

$isSubmit = isset($_POST['submit']);
if ($isSubmit) {
  $hasErrors = false;

  if (strlen(trim($title)) < 5) {
    Notice::error('Title too short. Make it at least 5 characters.');
    $hasErrors = true;
  }
  if (strlen(trim($content)) < 1) {
    Notice::error('Content too short. Make your post longer! Let your posts flow with all the words of the rainbow!');
    $hasErrors = true;
  }

  $options = array();

  if (User::can(User::RANK_ADMIN)) {
    if (getPost('isSticky')) {
      $options['isSticky'] = 1;
    }
    if (getPost('isClosed')) {
      $options['isClosed'] = 1;
    }
  }

  if (!$hasErrors) {
    $threadId = Threads::insertThread($title, $content, $forumId, $options);
    redirect('/forums/thread.php?threadId=' . $threadId);
  }


}

$Page->header('Create Thread');
$data['content'] = $content;
$data['title'] = $title;


view($data);