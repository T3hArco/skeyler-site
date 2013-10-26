<?php
if(!isset($isJson)) {
  $isJson = true;
}

require '../../_.php';

$Page = new Page();

function validateRank($rank = User::RANK_MOD) {
  if(!User::can($rank)) {
    Notice::error('What the he****ck!!!! You do not have permission to do this!!!!!');
    exit;
  }
}
validateRank();
