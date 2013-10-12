<?php
class Posts
{

  public static function bbcodeToHtml($str)
  {
    return $str;
  }

  public static function insertPost($content, $threadId) {
    global $DB, $User, $now;
    $query = '
      INSERT INTO posts (userId, timestamp, content, ip, threadId)
      VALUES(:userId, :now, :content, :ip, :threadId);
    ';
    $binds = array(
      'userId' => $User['id'],
      'now' => $now,
      'content' => $content,
      'ip' => ip2long($_SERVER['REMOTE_ADDR']),
      'threadId' => $threadId,
    );

    $DB->q($query, $binds);
  }



}