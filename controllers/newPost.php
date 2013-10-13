<?php
require '../_.php';
$Page = new Page();

$threadId = (int)(isset($_GET['threadId']) ? $_GET['threadId'] : 0);


$thread = Threads::load($threadId);

if (!$thread) {
  Notice::error('Thread does not exist.');
  exit;
}

$forum = Forums::load($thread['forumId']);

if (!$forum) {
  Notice::error('Oh no! That forum does not exist!');
  exit;
}

if ($User['rank'] < $forum['visibleRank']) {
  Notice::error('You do not have permission to view this forum.');
  exit;
}

$local['breadcrumbs'] = array(
  'Forums' => '/forums.php',
  $forum['name'] => '/forums.php?forumId=' . $thread['forumId'],
  $thread['title'] => '/thread.php?threadId=' . $threadId,
);

// they submitted the form

$content = isset($_POST['content']) ? $_POST['content'] : '';

$isSubmit = isset($_POST['submit']);
if ($isSubmit) {
  $hasErrors = false;
  if (strlen($content) < 5) {
    Notice::error('Content too short. Make it at least 5 characters.');
    $hasErrors = true;
  }

  if (!$hasErrors) {
    $lastPostId = Posts::insertPost($content, $threadId, $forum['id']);
    redirect('/thread.php?postId=' . $lastPostId);
  }


}

$Page->header('Create Thread');
$data['content'] = $content;


view($data);