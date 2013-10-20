<?php

class Forums
{

  public static function load($id)
  {
    global $DB;
    $query = 'SELECT * FROM forums WHERE id = ?;';
    return $DB->q($query, $id)->fetch();
  }

  public static function loadIds($forumIds)
  {
    global $DB;
    $forumIds = (array)$forumIds;
    if (count($forumIds) == 0) {
      return array();
    }

    $whereIn = DB::whereIn($forumIds);

    $query = '
      SELECT *
      FROM forums
      WHERE id IN(' . $whereIn . ')
      ;
    ';

    return populateIds($DB->q($query)->fetchAll());
  }

  public static function getForumAndSubforums($forumId)
  {
    global $DB, $User;
    $query = '
  SELECT f.id, f.name, f.description, f.postCount, f.threadCount, f.visibleRank, f.lastPostUserId, f.lastPostTimestamp, f.lastPostThreadId
  FROM forums AS f
  LEFT JOIN forum_parents AS fp
    ON fp.forumId = f.id
  WHERE (fp.parentId = :forumId OR f.id = :forumId) AND f.visibleRank <= :rank
  ORDER BY `order`
  ;
';

    $binds = array(
      'forumId' => $forumId,
      'rank' => $User['rank'],
    );
    return populateIds($DB->q($query, $binds)->fetchAll());
  }

  public static function getSubforumsFromForumList($forumIds)
  {
    global $DB, $User;

    if (count($forumIds) == 0) {
      return array();
    }

    $whereIn = DB::whereIn($forumIds);

    $query = '
      SELECT f.id, f.name, f.description, f.postCount, f.threadCount, f.visibleRank, f.lastPostUserId, f.lastPostTimestamp, f.lastPostThreadId, fp.parentId
      FROM forums AS f
      LEFT JOIN forum_parents AS fp
        ON fp.forumId = f.id
      WHERE fp.parentId IN(' . $whereIn . ') AND f.visibleRank <= :rank
      ORDER BY `order`
      ;
    ';

    $binds = array(
      'rank' => $User['rank'],
    );
    return populateIds($DB->q($query, $binds)->fetchAll());
  }

  public static function getAllVisibleForums()
  {
    global $User, $DB;
    $query = 'SELECT *
      FROM forums AS f
      WHERE visibleRank <= :rank
      ORDER BY `order`
    ';
    $binds = array(
      'rank' => $User['rank'],
    );
    return populateIds($DB->q($query, $binds)->fetchAll());
  }

  public static function getAllVisibleForumsGrouped()
  {
    global $User, $DB;
    $query = 'SELECT f.*, fp.parentId
      FROM forums AS f
        LEFT JOIN forum_parents AS fp
        ON f.id = fp.forumId
      WHERE f.visibleRank <= :rank
      ORDER BY f.`order`
    ';
    $binds = array(
      'rank' => $User['rank'],
    );
    $rows = $DB->q($query, $binds)->fetchAll();

    $forums = populateIds($rows);

    $parents = array();
    foreach ($forums as $forum) {
      if ($forum['parentId'] == '') {
        $forum['parentId'] = '_';
      }
      if (!isset($parents[$forum['parentId']])) {
        $parents[$forum['parentId']] = array();
      }
      $parents[$forum['parentId']][] = $forum;
    }

    $out = $parents['_'][0];

    function fillSubforums(&$forum, &$parents)
    {
      $forum['subforums'] = array();
      if (!isset($parents[$forum['id']])) {
        return;
      }
      foreach ($parents[$forum['id']] as $subforum) {
        $forum['subforums'][$subforum['id']] = $subforum;
        fillSubforums($forum['subforums'][$subforum['id']], $parents);
      }
    }

    fillSubforums($out, $parents);

    return $out;
  }

  public static function updateLastSeen($forumId)
  {
    global $DB, $User, $now;
    $query = '
      INSERT INTO forum_seen (forumId, userId, timestamp)
      VALUES(:forumId, :userId, :now)
      ON DUPLICATE KEY UPDATE timestamp = :now
    ';
    $binds = array(
      'forumId' => $forumId,
      'userId' => $User['id'],
      'now' => $now,
    );

    $DB->q($query, $binds);
  }


  public static function getLastSeen($forumIds)
  {
    global $User, $DB;

    if (count($forumIds) == 0) {
      return array();
    }

    $whereIn = DB::whereIn($forumIds);

    $query = '
    SELECT forumId, timestamp
    FROM forum_seen
    WHERE userId = :userId
      AND forumId IN(' . $whereIn . ')
    ;
    ';
    $binds = array(
      'userId' => $User['id'],
    );
    return populateIds($DB->q($query, $binds)->fetchAll(), 'forumId');

  }

  // recounts the threads and posts in a forum
  public static function recountCounts($forumId)
  {
    global $DB;

    $query = '
      SELECT COUNT(*) AS threadCount
      FROM threads
      WHERE forumId = :forumId
    ';

    $binds = array(
      'forumId' => $forumId,
    );
    $row = $DB->q($query, $binds)->fetch();
    $threadCount = $row['threadCount'];

    $query = '
      SELECT COUNT(*) AS postCount
      FROM posts AS p
      LEFT JOIN threads AS t ON p.threadId = t.id
      WHERE t.forumId = :forumId
    ';

    $binds = array(
      'forumId' => $forumId,
    );
    $row = $DB->q($query, $binds)->fetch();
    $postCount = $row['postCount'];

    $query = '
    UPDATE forums
    SET postCount = :postCount,
      threadCount = :threadCount
    WHERE id = :forumId
    LIMIT 1;';
    $binds = array(
      'threadCount' => $threadCount,
      'postCount' => $postCount,
      'forumId' => $forumId,
    );

    $DB->q($query, $binds);

  }

}




