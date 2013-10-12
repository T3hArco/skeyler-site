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
  ;
';

    $binds = array(
      'forumId' => $forumId,
      'rank' => $User['rank'],
    );
    return populateIds($DB->q($query, $binds)->fetchAll());
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

}




