<?php
require '_mod.php';

// check permission
validateRank(User::RANK_SUPER);

$userId = (int) getPost('userId');
$newRankVal = (int) getPost('newRank');

$user = User::getId($userId);

if (!$user) {
  Notice::error('That user don\'t exist, foo!');
  exit;
}

$oldRankVal = $user['rank'];
$oldRankStr = User::getRankStr($oldRankVal);

if (!User::can($user['rank'])) {
  Notice::error('Son of bitch! You can\'t change the rank of someone higher up than you!');
}
if (!User::can($newRankVal)) {
  Notice::error('Son of bitch! You can\'t promote someone with a higher rank than you! It\'s illegal!');
}

$newRankStr = User::getRankStr($newRankVal);

User::changeUserRank($userId, $newRankVal);

Notice::success('Wow you changed someone\'s rank! Good work!');

$data = array(
  'userId' => $userId,
  'oldRankVal' => $oldRankVal,
  'oldRankStr' => $oldRankStr,
  'newRankVal' => $newRankVal,
  'newRankStr' => $newRankStr,
  'success' => true,
);

view($data);