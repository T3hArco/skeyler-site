<?php
require '../_.php';

global $User, $Config, $pageId;

$Page = new Page('forums');
$Page->setClasses('page-thread');

$threadId = (int)getGet('threadId');
$postId = (int)getGet('postId');
$lastPost = isset($_GET['lastPost']);

if ($postId && !$threadId) {
  $post = Posts::load($postId);
  if ($post) {
    $threadId = $post['threadId'];
    $postCount = Posts::getPostCountForPostIdByThread($post);
    $pageId = floor(($postCount - 1) / $Config['postsPerPage']) + 1;
    $postNum = (($postCount - 1) % $Config['postsPerPage']) + 1;
    redirect('/thread.php?threadId=' . $threadId . '&page=' . $pageId . '#p_' . $postNum);
  }
}

$thread = Threads::load($threadId);

if($lastPost) {
  $postCount = $thread['postCount'];
  $pageId = floor(($postCount - 1) / $Config['postsPerPage']) + 1;
  $postNum = (($postCount - 1) % $Config['postsPerPage']) + 1;
  redirect('/thread.php?threadId=' . $threadId . '&page=' . $pageId . '#p_' . $postNum);
}

if (!$thread) {
  Notice::error('Thread does not exist.');
  exit;
}

$forum = Forums::load($thread['forumId']);

if (!$forum) {
  Notice::error('What in the ache ee double hockey sticks!!?? Thread could not be matched up to a forum');
  exit;
}

$local['breadcrumbs'] = array(
  'Forums' => '/forums.php',
  $forum['name'] => '/forums.php?forumId=' . $thread['forumId'],
  $thread['title'] => '/thread.php?threadId=' . $threadId,
);

if ($User['rank'] < $forum['visibleRank']) {
  Notice::error('You do not have permission to view this thread.');
  exit;
}

$posts = Threads::getPosts($threadId, $pageId);

if (!$posts) {
  Notice::error('Can\'t find any posts in that thread! Oh no!');
  exit;
}

$userIds = array_unique(array_merge(eachField($posts, 'userId'), eachField($posts, 'lastEditUserId')));
$users = User::loadIds($userIds);

$postsSeen = max(0, min($pageId * $Config['postsPerPage'], count($posts) + (($pageId - 1) * $Config['postsPerPage'])));

// marks the forum as seen at the current time by the current user
Threads::updateLastSeen($threadId, $postsSeen);

$data['thread'] = $thread;
$data['forum'] = $forum;
$data['posts'] = $posts;
$data['users'] = $users;

$Page->header($thread['title']);
view($data);