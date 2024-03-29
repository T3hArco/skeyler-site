<?php
require '_forums.php';
$Page = new Page('forums');

$postId = (int)getGet('postId');

$post = Posts::load($postId);

if (!$post) {
  Notice::error('Could not find the post you want to edit!');
  exit;
}

$thread = Threads::load($post['threadId']);
if (!$thread) {
  Notice::error('That post isn\'t in a thread! What the heck!');
  exit;
}

$forum = Forums::load($thread['forumId']);

if (!$forum) {
  Notice::error('Oh no! That forum does not exist!');
  exit;
}

if ($User['rank'] < $forum['visibleRank'] || $User['rank'] < $forum['createPostRank']) {
  Notice::error('You do not have permission to edit this post.');
  exit;
}

if($User['id'] != $post['userId'] && !User::can(User::RANK_MOD)) {
  Notice::error('You cannot edit someone else\'s post!');
  exit;
}

if($thread['isClosed'] && !User::can(User::RANK_MOD)) {
  Notice::error('This thread is closed. You can\'t edit posts in closed threads. Contact someone on staff if you need it modified.');
}

$local['breadcrumbs'] = array(
  'Forums' => '/forums/',
  $forum['name'] => '/forums/?forumId=' . $thread['forumId'],
  $thread['title'] => '/forums/thread.php?threadId=' . $thread['id'],
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
    Posts::editPost($content, $postId);
    redirect('/forums/thread.php?postId=' . $postId);
  }
}

$content = getPost('content');
if(strlen($content) == 0) {
  $content = $post['content'];
}

$Page->header('Edit a Post');
$data['content'] = $content;
$data['thread'] = $thread;
$data['post'] = $post;


view($data);