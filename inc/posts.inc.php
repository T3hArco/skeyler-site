<?php
class Posts
{

  public static function load($id)
  {
    global $DB;
    $query = 'SELECT * FROM posts WHERE id = ?;';
    return $DB->q($query, $id)->fetch();
  }

  public static function loadIds($postIds)
  {
    global $DB;
    $postIds = (array)$postIds;
    if (count($postIds) == 0) {
      return array();
    }

    $whereIn = DB::whereIn($postIds);

    $query = '
      SELECT *
      FROM posts
      WHERE id IN(' . $whereIn . ')
      ;
    ';

    return populateIds($DB->q($query)->fetchAll());
  }

  // returns the post # in the thread
  public static function getPostCountForPostIdByThread($post)
  {
    global $DB;
    $query = 'SELECT COUNT(*) AS postCount FROM posts WHERE id <= :postId AND threadId = :threadId LIMIT 1;';
    $binds = array(
      'postId' => $post['id'],
      'threadId' => $post['threadId'],
    );
    $row = $DB->q($query, $binds)->fetch();
    if (!$row) {
      return 0;
    }
    return $row['postCount'];
  }

  public static function insertPost($content, $threadId, $isNewThread = false)
  {
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