<?php
require '_api.php';

$forumList = Forums::getAllVisibleForumsGrouped();

$data['forumList'] = $forumList;

view($data);
