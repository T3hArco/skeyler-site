<?php
$isJson = true;
require '../../_.php';
$Page = new Page();
$Page->header();

$forumList = Forums::getAllVisibleForumsGrouped();

$data['forumList'] = $forumList;

view($data);
