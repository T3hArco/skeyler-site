<?php
class Posts
{

  public static function load($id) {
    $query = 'SELECT * FROM posts WHERE id = ?;';
    return DB::q($query, $id)->fetch();
  }

  public static function loadIds($postIds) {
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

    return populateIds(DB::q($query)->fetchAll());
  }

  // returns the post # in the thread
  public static function getPostCountForPostIdByThread($post) {
    $query = 'SELECT COUNT(*) AS postCount FROM posts WHERE id <= :postId AND threadId = :threadId LIMIT 1;';
    $binds = array(
      'postId' => $post['id'],
      'threadId' => $post['threadId'],
    );
    $row = DB::q($query, $binds)->fetch();
    if (!$row) {
      return 0;
    }
    return $row['postCount'];
  }

  public static function insertPost($content, $threadId, $forumId, $isNewThread = false) {
    global $User, $now;


      DB::beginTransaction();

    $query = '
      INSERT INTO posts (userId, timestamp, content, contentParsed, ip, threadId)
      VALUES(:userId, :now, :content, :contentParsed, :ip, :threadId);
    ';
    $binds = array(
      'userId' => $User['id'],
      'now' => $now,
      'content' => $content,
      'contentParsed' => BBCode::parse($content),
      'ip' => ip2long($_SERVER['REMOTE_ADDR']),
      'threadId' => $threadId,
    );

    DB::q($query, $binds);

    $lastPostId = DB::lastInsertId();

    $query = 'UPDATE threads SET postCount = postCount + 1, lastPostUserId = :lastPostUserId, lastPostTimestamp = :now WHERE id = :threadId';
    $binds = array(
      'threadId' => $threadId,
      'lastPostUserId' => $User['id'],
      'now' => $now,
    );
    DB::q($query, $binds);

//    $query = 'UPDATE forums SET postCount = postCount + 1, threadCount = threadCount + :threadInc, lastPostUserId = :lastPostUserId, lastPostTimestamp = :now, lastPostThreadId = :threadId WHERE id = :forumId';
//    $binds = array(
//      'threadInc' => ($isNewThread ? 1 : 0),
//      'lastPostUserId' => $User['id'],
//      'now' => $now,
//      'threadId' => $threadId,
//      'forumId' => $forumId,
//    );
//    DB::q($query, $binds);

    $query = '
      UPDATE forums SET postCount = postCount + 1, threadCount = threadCount + :threadInc, lastPostUserId = :lastPostUserId, lastPostTimestamp = :now, lastPostThreadId = :threadId WHERE
      id IN (SELECT parentId FROM forum_parents WHERE forumId = :forumId) OR id = :forumId;
    ';
    $binds = array(
      'threadInc' => ($isNewThread ? 1 : 0),
      'lastPostUserId' => $User['id'],
      'now' => $now,
      'threadId' => $threadId,
      'forumId' => $forumId,
    );
    DB::q($query, $binds);

    $postInc = 1;

    // postcounts dont increase on the arcade forum, because it is terrible!
    // this is hard coded because it's a one-off
    if($forumId == 20){
      $postInc = 0;
    }

    $query = 'UPDATE users SET postCount = postCount + :postInc WHERE id = :userId';
    $binds = array(
      'userId' => $User['id'],
      'postInc' => $postInc,
    );
    DB::q($query, $binds);

    DB::commit();
    return $lastPostId;

  }

  public static function editPost($content, $postId) {
    global $User, $now;
    $query = '
      UPDATE posts
      SET content = :content,
        contentParsed = :contentParsed,
        lastEditTimestamp = :now,
        lastEditUserId = :userId
      WHERE id = :postId
      LIMIT 1;
    ';
    $binds = array(
      'userId' => $User['id'],
      'now' => $now,
      'content' => $content,
      'contentParsed' => BBCode::parse($content),
      'postId' => $postId,
    );
    DB::q($query, $binds);
  }

  public static function delete($post) {
    $thread = Threads::load($post['threadId']);

    DB::beginTransaction();

    // find how deep into the thread the post is
    $query = '
      SELECT COUNT(*) AS postCount
      FROM posts
      WHERE threadId = :threadId
      AND id <= :postId;
    ';
    $binds = array(
      'threadId' => $post['threadId'],
      'postId' => $post['id'],
    );
    $postCount =DB::q($query, $binds)->fetch();
    $postCount = $postCount['postCount'];

    $query = '
      UPDATE thread_seen
      SET postsSeen = postsSeen - 1
      WHERE threadId = :threadId
        AND postsSeen >= :postCount
    ';
    $binds = array(
      'threadId' => $post['threadId'],
      'postCount' => $postCount,
    );
    DB::q($query, $binds);

    // move post to new location
    $query = '
      UPDATE posts SET threadId = -1
      WHERE id = :postId
      LIMIT 1;
    ';
    $binds = array(
      'postId' => $post['id'],
    );
    DB::q($query, $binds);

    // update thread post count
    $query = '
      UPDATE threads SET postCount = postCount - 1
      WHERE id = :threadId
      LIMIT 1;
    ';
    $binds = array(
      'threadId' => $thread['id'],
    );
    DB::q($query, $binds);

    // update deleted thread post count
    $query = '
      UPDATE threads SET postCount = postCount + 1
      WHERE id = -1
      LIMIT 1;
    ';
    DB::q($query);

    // update forum post count
    $query = '
      UPDATE forums SET postCount = postCount - 1
      WHERE id = :forumId
      LIMIT 1;
    ';
    $binds = array(
      'forumId' => $thread['forumId'],
    );
    DB::q($query, $binds);

    // update deleted forum post count
    $query = '
      UPDATE forums SET postCount = postCount + 1
      WHERE id = -1
      LIMIT 1;
    ';
    DB::q($query);

    DB::commit();
  }

}