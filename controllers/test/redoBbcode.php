<?php
require '_test.php';

$Page = new Page();

if (!getGet('key') == 'ehaLdas7df') {
  Notice::error('No.');
  exit;
}

$Page->header('Recalculate all bbcode or something');

$query = 'SELECT * FROM posts;';
$posts = DB::q($query)->fetchAll();

$count = 0;

foreach ($posts as $post) {
  $query = 'UPDATE posts SET contentParsed = :parsed WHERE id = :postId LIMIT 1;';
  $binds = array(
    'parsed' => BBCode::parse($post['content']),
    'postId' => $post['id'],
  );
  DB::q($query, $binds);
  $count++;
}

var_dump($count);

Notice::success('Damn, son, you crazy. All bbcode has been reprocessed');

var_dump(444);

view(array(), '/test/redoBbcode.php');