<?php
require '../_.php';
$Page = new Page('servers');


$serverIps = array(
  '208.115.236.184:27017',
//  '208.115.236.184:27015',
//  '208.115.236.184:27016',
//  '208.115.236.184:27018',
//  '208.115.236.184:27019',
  '208.115.236.184:27030',
  '208.115.236.184:27031',
);

$servers = array();

foreach($serverIps as $ip){
  $servers[] = a2s_info($ip);
}

$Page->header();


$data['servers'] = $servers;
view($data);