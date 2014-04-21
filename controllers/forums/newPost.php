<?php
require '_forums.php';
$Page = new Page('forums');

$threadId = (int) getGet('threadId');

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

if ($User['rank'] < $forum['visibleRank'] || $User['rank'] < $forum['createPostRank']) {
  Notice::error('You do not have permission to post on this.');
  exit;
}

if ($thread['isClosed'] && !User::can(User::RANK_MOD)) {
  Notice::error('This thread is closed. You can\'t post anything else in it');
}

$local['breadcrumbs'] = array(
  'Forums' => '/forums/',
  $forum['name'] => '/forums/?forumId=' . $thread['forumId'],
  $thread['title'] => '/forums/thread.php?threadId=' . $threadId,
);

// they submitted the form

$content = getPost('content');

$isSubmit = isset($_POST['submit']);
if ($isSubmit) {
  $hasErrors = false;
  if (strlen(trim($content)) < 1) {
    Notice::error('Content too short. Make your post longer! Let your posts flow with all the words of the rainbow!');
    $hasErrors = true;
  }

  if (!$hasErrors) {
    // if they made the last post in the thread and its been less than 2 hours, make it edit their previous post instead.
    if ($thread['lastPostTimestamp'] > $now - 7200 && $User['id'] == $thread['lastPostUserId']) {
      $postId = Posts::appendEditPost($content, $thread['id'], $User['id']);
      redirect('/forums/thread.php?postId=' . $postId);
    }
    else {
      // otherwise, post it!
      $lastPostId = Posts::insertPost($content, $threadId, $forum['id']);
      redirect('/forums/thread.php?postId=' . $lastPostId);
    }


  }


}

$Page->header('Reply to Thread');
$data['content'] = $content;
$data['thread'] = $thread;


view($data);