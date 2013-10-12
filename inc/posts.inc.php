<?php
class Posts
{

  public static function bbcodeToHtml($str)
  {
    return $str;
  }

  public static function insertPost($content, $threadId, $isNewThread = false) {
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

    $query = 'UPDATE threads SET postCount = postCount + 1, lastPostUserId = :lastPostUserId, lastPostTimestamp = :now WHERE id = :threadId';
    $binds = array(
      'threadId' => $threadId,
      'lastPostUserId' => $User['id'],
      'now' => $now,
    );
    $DB->q($query, $binds);


    $query = 'UPDATE forums SET postCount = postCount + 1, threadCount = threadCount + :threadInc, lastPostUserId = :lastPostUserId, lastPostTimestamp = :now, lastPostThreadId = :threadId';
    $binds = array(
      'threadInc' => ($isNewThread ? 1 : 0),
      'lastPostUserId' => $User['id'],
      'now' => $now,
      'threadId' => $threadId,
    );
    $DB->q($query, $binds);

    $query = 'UPDATE users SET postCount = postCount + 1 WHERE id = :userId';
    $binds = array(
      'userId' => $User['id'],
    );
    $DB->query($query, $binds);


  }



}