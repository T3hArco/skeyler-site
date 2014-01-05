<?php
require '_api.php';

if(!$isLoggedIn) {
  Notice::error('You are not logged in.');
  exit;
}

$content = getPost('content');
if(strlen($content) == 0) {
  Notice::error('Message too short.');
  exit;
}

$query = 'INSERT INTO chatbox(userId, timestamp, content) VALUES(:userId, :now, :content);';
$binds = array(
  'userId' => $User['id'],
  'now' => $now,
  'content' => $content,
);
DB::q($query, $binds);

$data['timestamp'] = $now;
$data['rankStr'] = User::getRankStr($User['rank']);
$data['userId'] = $User['id'];
$data['username'] = $User['name'];
$data['content'] = $content;
$data['success'] = true;

view($data);