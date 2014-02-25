<?php
require '_mod.php';

$ranks = array(
  User::RANK_REGULAR,
  User::RANK_VIP,
  User::RANK_MOD,
  User::RANK_ADMIN,
  User::RANK_DEV,
  User::RANK_SUPER,
  User::RANK_OWNER,
);

asort($ranks);

$out = '';
$out = '<h5>Here are the ranks!!!</h5>';

foreach ($ranks as $rankVal) {
  $out .= $rankVal . ': ' . User::writeRankTag($rankVal) . ' - ' . User::getRankStr($rankVal) . '<br/>';
}

$data = array(
  'out' => $out,
  'success' => true,
);

view($data);