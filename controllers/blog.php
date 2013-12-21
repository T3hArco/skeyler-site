<?php
require '../_.php';
$Page = new Page('news');
$Page->header('Blog');

$info = Threads::getBlogPosts($pageId);

$data = array();


$data['threads'] = $info['threads'];
$data['posts'] = $info['posts'];
$data['users'] = $info['users'];

view($data);




