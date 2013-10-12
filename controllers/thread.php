<?php
require '../_.php';

global $User, $Config, $pageId;

$Page = new Page();

$threadId = (int)(isset($_GET['threadId']) ? $_GET['threadId'] : 0);

$thread = Threads::load($threadId);

if (!$thread) {
  Notice::error('Thread does not exist.');
  exit;
}

$forum = Forums::load($thread['forumId']);

if (!$forum) {
  Notice::error('What in the ache ee double hockey sticks!!?? Thread could not be matched up to a forum');
  exit;
}

if ($User['rank'] < $forum['visibleRank']) {
  Notice::error('You do not have permission to view this thread.');
  exit;
}

$posts = Threads::getPosts($threadId, $pageId);

if (!$posts) {
  Notice::error('Can\'t find any posts in that thread! Oh no!');
  exit;
}

$userIds = array_unique(eachField($posts, 'userId'));
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