<?php class Threads
{

  public static function load($id) {
    $query = 'SELECT * FROM threads WHERE id = ?;';
    return DB::q($query, $id)->fetch();
  }

  public static function loadIds($threadIds) {
    $threadIds = (array) $threadIds;
    if (count($threadIds) == 0) {
      return array();
    }

    $whereIn = DB::whereIn($threadIds);

    $query = '
      SELECT *
      FROM threads
      WHERE id IN(' . $whereIn . ')
      ;
    ';

    return populateIds(DB::q($query)->fetchAll());
  }

  public static function loadFromForum($forumId) {
    global $pageId, $Config;
    $query = '
    SELECT *
    FROM threads
    WHERE forumId = :forumId
    ORDER BY isSticky DESC, lastPostTimestamp DESC
    LIMIT ' . ($Config['threadsPerPage'] * ($pageId - 1)) . ', ' . $Config['threadsPerPage'] . '

    ;';
    $binds = array(
      'forumId' => $forumId,
    );
    return populateIds(DB::q($query, $binds)->fetchAll());
  }


  public static function updateLastSeen($threadId, $postsSeen) {
    global $User;

    if(!$User['id']) {
      return false;
    }

    DB::beginTransaction();

    $query = '
      INSERT INTO thread_seen (threadId, userId, postsSeen)
        VALUES(:forumId, :userId, :postsSeen)
      ON DUPLICATE KEY UPDATE
        postsSeen = GREATEST(postsSeen, :postsSeen)
    ';
    // who cares about on big primary keys here
    $binds = array(
      'forumId' => $threadId,
      'userId' => $User['id'],
      'postsSeen' => $postsSeen,
    );

    DB::q($query, $binds);

    $query = 'UPDATE threads SET views = views + 1 WHERE id = :threadId';
    $binds = array(
      'threadId' => $threadId,
    );

    DB::q($query, $binds);

    DB::commit();
  }


  public static function getLastReads($threadIds) {
    global $User;

    if (count($threadIds) == 0) {
      return array();
    }

    $whereIn = DB::whereIn($threadIds);

    $query = '
    SELECT threadId, postsSeen
    FROM thread_seen
    WHERE userId = :userId
      AND threadId IN(' . $whereIn . ')
    ;
    ';
    $binds = array(
      'userId' => $User['id'],
    );
    return populateIds(DB::q($query, $binds)->fetchAll(), 'threadId');

  }

  public static function getPosts($threadId, $pageNum) {
    global $Config;

    $query = '
    SELECT *
    FROM posts
    WHERE threadId = :threadId
    LIMIT ' . ($pageNum - 1) * $Config['postsPerPage'] . ', ' . $Config['postsPerPage'] . '
    ';

    $binds = array(
      'threadId' => $threadId,
    );

    return populateIds(DB::q($query, $binds)->fetchAll());

  }

  public static function insertThread($title, $content, $forumId, $options = array()) {
    global $User, $now;

    if (!isset($options['isSticky'])) {
      $options['isSticky'] = 0;
    }
    if (!isset($options['isClosed'])) {
      $options['isClosed'] = 0;
    }

    DB::beginTransaction();

    $query = '
      INSERT INTO threads (userId, title, forumId, lastPostUserId, lastPostTimestamp, isSticky, isClosed)
      VALUES(:userId, :title, :forumId, :userId, :now, :isSticky, :isClosed);
    ';
    $binds = array(
      'userId' => $User['id'],
      'title' => $title,
      'forumId' => $forumId,
      'now' => $now,
      'isSticky' => $options['isSticky'],
      'isClosed' => $options['isClosed'],
    );
    DB::q($query, $binds);
    $threadId = DB::lastInsertId();

    Posts::insertPost($content, $threadId, $forumId, true);

    DB::commit();

    return $threadId;
  }

  public static function close($threadId) {
    $query = 'UPDATE threads SET isClosed = 1 WHERE id = :threadId LIMIT 1';
    $binds = array(
      'threadId' => $threadId,
    );
    DB::q($query, $binds);
  }

  public static function open($threadId) {
    $query = 'UPDATE threads SET isClosed = 0 WHERE id = :threadId LIMIT 1';
    $binds = array(
      'threadId' => $threadId,
    );
    DB::q($query, $binds);
  }

  public static function sticky($threadId) {
    $query = 'UPDATE threads SET isSticky = 1 WHERE id = :threadId LIMIT 1';
    $binds = array(
      'threadId' => $threadId,
    );
    DB::q($query, $binds);
  }

  public static function unsticky($threadId) {
    $query = 'UPDATE threads SET isSticky = 0 WHERE id = :threadId LIMIT 1';
    $binds = array(
      'threadId' => $threadId,
    );
    DB::q($query, $binds);
  }

  public static function rename($threadId, $title) {
    $query = 'UPDATE threads SET title = :title WHERE id = :threadId LIMIT 1';
    $binds = array(
      'threadId' => $threadId,
      'title' => $title,
    );
    DB::q($query, $binds);
  }

  public static function move($threadId, $destinationForumId) {

    $thread = Threads::load($threadId);

    DB::beginTransaction();

    $query = 'UPDATE threads SET forumId = :forumId WHERE id = :threadId LIMIT 1';
    $binds = array(
      'threadId' => $threadId,
      'forumId' => $destinationForumId,
    );
    DB::q($query, $binds);
    // recount threads/posts for both forums

    Forums::recountCounts($thread['forumId']);
    Forums::recountCounts($destinationForumId);

    DB::commit();

  }

  public static function delete($threadId) {
    self::move($threadId, -1);
  }

  public static function recountAll() {
    $query = '
      SELECT threadId, COUNT(id) AS postCount
      FROM posts
      GROUP BY threadId
    ';
    $counts = DB::q($query)->fetchAll();

    foreach ($counts as $count) {
      $query = '
        UPDATE threads
        SET postCount = :postCount
        WHERE id = :threadId
        LIMIT 1;
      ';
      $binds = array(
        'threadId' => $count['threadId'],
        'postCount' => $count['postCount'],
      );
      var_dump($query, $binds);
      DB::q($query, $binds);
    }

  }

  public static function getBlogPosts($pageId) {
    global $Config;
    $query = '
      SELECT *
      FROM threads
      WHERE forumId = 1
      ORDER BY id DESC
      LIMIT ' . ($pageId - 1) * $Config['blogPostsPerPage'] . ', ' . $Config['blogPostsPerPage'] . '
      ';
    $threads = DB::q($query)->fetchAll();

    $out['threads'] = $threads;

    $threadIds = eachField($threads);
    $posts = array();

    if (count($threadIds) > 0) {
      $whereIn = DB::whereIn($threadIds);
      $query = '
        SELECT MIN(id) AS postId
        FROM posts
        WHERE threadId IN(' . $whereIn . ')
        GROUP BY threadId
        ;
      ';
      $postIds = eachField(DB::q($query)->fetchAll(), 'postId');

      $posts = populateIds(Posts::loadIds($postIds), 'threadId');

    }

    $out['posts'] = $posts;

    $userIds = eachField($posts, 'userId');
    $out['users'] = User::loadIds($userIds);


    return $out;
  }

}