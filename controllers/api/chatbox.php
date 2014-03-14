<?php
require '_api.php';

$max = 20;
$id = (int)getGet('id');

$query = 'SELECT * FROM chatbox WHERE id > :id ORDER BY id DESC LIMIT ' . $max . ';';
$binds = array(
  'id' => $id,
);

$chats = populateIds(DB::q($query, $binds)->fetchAll());
$chatIds = eachField($chats, 'id');

$userIds = eachField($chats, 'userId');

$users = array();
if (count($userIds)) {
  $whereIn = DB::whereIn($userIds);
  $query = 'SELECT id, name, rank FROM users WHERE id IN(' . $whereIn . ');';
  $users = populateIds(DB::q($query)->fetchAll());
}

foreach ($users as $userId => $user) {
  $users[$userId]['rankStr'] = User::getRankStr($user['rank']);
}

$data['chats'] = $chats;
$data['users'] = $users;
$data['highestId'] = $chatIds ? max($chatIds) : 0;
$data['lowestId'] = $chatIds ? min($chatIds) : 0;
$data['maxCount'] = $max;
$data['success'] = true;

view($data);

