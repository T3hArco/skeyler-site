<?php
require '../_.php';
$Page = new Page('servers');


$serverIps = array(
  '63.143.48.134:27017', // Lobby1
//  '63.143.48.134:27015', // Sass2
//  '63.143.48.134:27016', // Sass3
//  '63.143.48.134:27018', // Sass4
//  '63.143.48.134:27019', // Sass5
  '63.143.48.134:27031', // Deathrun6
  '63.143.48.134:27030', // Bunny Hop7
);

$servers = array();

foreach($serverIps as $ip){
  $servers[] = a2s_info($ip);
}

$Page->header();


$data['servers'] = $servers;
view($data);