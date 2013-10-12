<?php class Threads
{

  public static function load($id)
  {
    global $DB;
    $query = 'SELECT * FROM threads WHERE id = ?;';
    return $DB->q($query, $id)->fetch();
  }

  public static function loadIds($threadIds)
  {
    global $DB;
    $threadIds = (array)$threadIds;
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

    return populateIds($DB->q($query)->fetchAll());
  }

  public static function loadFromForum($forumId)
  {
    global $DB;
    $query = 'SELECT * FROM threads WHERE forumId = ?;';
    return populateIds($DB->q($query, $forumId)->fetchAll());
  }


  public static function updateLastSeen($threadId, $postsSeen)
  {
    global $DB, $User;
    $query = '
      INSERT INTO thread_seen (threadId, userId, postsSeen)
        VALUES(:forumId, :userId, :postsSeen)
      ON DUPLICATE KEY UPDATE
        postsSeen = GREATEST(postsSeen, :postsSeen)
    ';
    $binds = array(
      'forumId' => $threadId,
      'userId' => $User['id'],
      'postsSeen' => $postsSeen,
    );

    $DB->q($query, $binds);
  }


  public static function getLastReads($threadIds)
  {
    global $User, $DB;

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
    return populateIds($DB->q($query, $binds)->fetchAll(), 'threadId');

  }

  public static function getPosts($threadId, $pageNum) {
    global $Config, $DB;

    $query = '
    SELECT *
    FROM posts
    WHERE threadId = :threadId
    LIMIT ' . ($pageNum - 1) * $Config['postsPerPage'] . ', ' . $Config['postsPerPage'] . '
    ';

    $binds = array(
      'threadId' => $threadId,
    );

    return populateIds($DB->q($query, $binds)->fetchAll());

  }


}