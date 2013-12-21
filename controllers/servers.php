<?php
require '../_.php';
$Page = new Page('servers');


$servers = array(
  array(
    'name' => '[SS#1] Deathrun',
    'ip' => '192.168.1.1',
    'port' => '27015',
    'map' => 'dr_carbonic2!!!!',
    'maxPlayers' => 40,
    'currentPlayers' => 20,
    'gamemode' => '[SS] Deathrun',
  ),
  array(
    'name' => '[SS#2] Bhop',
    'ip' => '192.168.1.2',
    'port' => '27015',
    'map' => 'bhop_carbonic2!!!!',
    'maxPlayers' => 40,
    'currentPlayers' => 10,
    'gamemode' => '[SS] Bhop',
  ),
);

$Page->header();


$data['servers'] = $servers;
view($data);