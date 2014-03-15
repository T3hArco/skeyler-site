<?php
require '../../_.php';

function validateRank($rank = User::RANK_DEV) {
  if (!User::can($rank)) {
    Notice::error('What the he****ck!!!! You do not have permission to do this!!!!!');
    exit;
  }
}

validateRank();
