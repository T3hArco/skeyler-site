<?php
require '_mod.php';

// check permission
validateRank(User::RANK_ADMIN);

$postId = (int) getPost('postId');
$post = Posts::load($postId);

if (!$post) {
  Notice::error('Could not find the post you want to delete!!!!');
  exit;
}

$threadId = $post['threadId'];
$thread = Threads::load($threadId);
if ($thread['postCount'] == 1) {
  $data = array(
    'noPostsRemain' => true,
    'success' => false,
  );
  view($data);
  exit;
}

Posts::delete($post);

Notice::success('You deleted a post! It\'s now in the Deleted Post Shanty! Well done!');

$data = array(
  'postId' => $postId,
  'success' => true,
);

view($data);