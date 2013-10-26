<?php
$isJson = false;
require '_mod.php';

$Page->header('Edit News');

$newNews = getPost('news');

if(strlen($newNews) > 0) {
  $newsJson = array_filter(preg_split('#(?:\r?\n)+#', $newNews));
  Cache::save('news', $newsJson);
}

$newsJson = Cache::load('news');

$news = implode("\n\n", $newsJson);

$data = array(
  'news' => $news,
);

view($data);

