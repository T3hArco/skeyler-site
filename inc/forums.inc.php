<?php

class Forums
{

  public static function load($id) {
    $query = 'SELECT * FROM forums WHERE id = ?;';
    return DB::q($query, $id)->fetch();
  }

  public static function loadIds($forumIds) {
    $forumIds = (array) $forumIds;
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

    return populateIds(DB::q($query)->fetchAll());
  }

  public static function getForumAndSubforums($forumId) {
    global $User;
    $query = '
  SELECT f.id, f.name, f.description, f.postCount, f.threadCount, f.visibleRank, f.createPostRank, f.createThreadRank, f.lastPostUserId, f.lastPostTimestamp, f.lastPostThreadId
  FROM forums AS f
  LEFT JOIN forum_parents AS fp
    ON fp.forumId = f.id AND fp.isMainParent = 1
  WHERE (fp.parentId = :forumId OR f.id = :forumId) AND f.visibleRank <= :rank
  ORDER BY `order`
  ;
';

    $binds = array(
      'forumId' => $forumId,
      'rank' => $User['rank'],
    );
    return populateIds(DB::q($query, $binds)->fetchAll());
  }

  public static function getSubforumsFromForumList($forumIds) {
    global $User;

    if (count($forumIds) == 0) {
      return array();
    }

    $whereIn = DB::whereIn($forumIds);

    $query = '
      SELECT f.id, f.name, f.description, f.postCount, f.threadCount, f.visibleRank, f.lastPostUserId, f.lastPostTimestamp, f.lastPostThreadId, fp.parentId
      FROM forums AS f
      LEFT JOIN forum_parents AS fp
        ON fp.forumId = f.id AND fp.isMainParent = 1
      WHERE fp.parentId IN(' . $whereIn . ') AND f.visibleRank <= :rank
      ORDER BY `order`
      ;
    ';

    $binds = array(
      'rank' => $User['rank'],
    );
    return populateIds(DB::q($query, $binds)->fetchAll());
  }

  public static function getAllVisibleForums() {
    global $User;
    $query = 'SELECT *
      FROM forums AS f
      WHERE visibleRank <= :rank
      ORDER BY `order`
    ';
    $binds = array(
      'rank' => $User['rank'],
    );
    return populateIds(DB::q($query, $binds)->fetchAll());
  }

  public static function getAllVisibleForumsGrouped() {
    global $User;
    $query = 'SELECT f.*, fp.parentId
      FROM forums AS f
        LEFT JOIN forum_parents AS fp
        ON f.id = fp.forumId AND fp.isMainParent = 1
      WHERE f.visibleRank <= :rank
      ORDER BY f.`order`
    ';
    $binds = array(
      'rank' => $User['rank'],
    );
    $rows = DB::q($query, $binds)->fetchAll();

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

    function fillSubforums(&$forum, &$parents) {
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

  public static function updateLastSeen($forumId) {
    global $User, $now;

    if (!$User['id']) {
      return false;
    }

    $query = '
      INSERT INTO forum_seen (forumId, userId, timestamp)
      VALUES(:forumId, :userId, :now)
      ON DUPLICATE KEY UPDATE timestamp = :now
    ';
    // who cares about primary keys here? but might want to make it bigint?
    $binds = array(
      'forumId' => $forumId,
      'userId' => $User['id'],
      'now' => $now,
    );

    DB::q($query, $binds);
  }


  public static function getLastSeen($forumIds) {
    global $User;

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
    return populateIds(DB::q($query, $binds)->fetchAll(), 'forumId');

  }

  // recounts the threads and posts in a forum
  public static function recountCounts($forumId) {

    $query = '
      SELECT COUNT(*) AS threadCount
      FROM threads
      WHERE forumId = :forumId
    ';

    $binds = array(
      'forumId' => $forumId,
    );
    $row = DB::q($query, $binds)->fetch();
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
    $row = DB::q($query, $binds)->fetch();
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

    DB::q($query, $binds);

  }

  public static function rename($forumId, $name, $description = null) {
    if (is_null($description)) {
      $query = 'UPDATE forums SET name = :name WHERE id = :forumId LIMIT 1';
      $binds = array(
        'forumId' => $forumId,
        'name' => $name,
      );
    }
    else {
      $query = 'UPDATE forums SET name = :name, description = :description WHERE id = :forumId LIMIT 1';
      $binds = array(
        'forumId' => $forumId,
        'name' => $name,
        'description' => $description,
      );
    }
    DB::q($query, $binds);
  }

  public static function createForum($parentId, $name, $description, $visibleRank, $createPostRank, $createThreadRank) {
    DB::beginTransaction();

    $query = '
      INSERT INTO forums
      (name, description, visibleRank, createPostRank, createThreadRank)
      VALUES(:name, :description, :visibleRank, :createPostRank, :createThreadRank)
    ';
    $binds = array(
      'name' => $name,
      'description' => $description,
      'visibleRank' => $visibleRank,
      'createPostRank' => $createPostRank,
      'createThreadRank' => $createThreadRank,
    );
    DB::q($query, $binds);

    $forumId = DB::lastInsertId();

    $query = '
      INSERT INTO forum_parents(forumId, parentId, isMainParent)
      VALUES(:forumId, :parentId, 1);
    ';
    $binds = array(
      'forumId' => $forumId,
      'parentId' => $parentId,
    );
    DB::q($query, $binds);

    $query = '
      INSERT INTO forum_parents(forumId, parentId, isMainParent)
      SELECT :forumId, parentId, 0 FROM forum_parents WHERE forumId = :parentId;
    ;';

    $binds = array(
      'forumId' => $forumId,
      'parentId' => $parentId,
    );

    DB::q($query, $binds);

    DB::commit();
    return $forumId;
  }

  public static function editForum($forumId, $name, $description, $visibleRank, $createPostRank, $createThreadRank) {
    $query = '
      UPDATE forums
      SET
        `name` = :name,
        description = :description,
        visibleRank = :visibleRank,
        createPostRank = :createPostRank,
        createThreadRank = :createThreadRank
      WHERE id = :forumId
      LIMIT 1;
    ';
    $binds = array(
      'name' => $name,
      'description' => $description,
      'visibleRank' => $visibleRank,
      'createPostRank' => $createPostRank,
      'createThreadRank' => $createThreadRank,
      'forumId' => $forumId,
    );
    DB::q($query, $binds);
    return DB::lastInsertId();
  }

  public static function getParents($forumId){
    $query = '
      SELECT f.id, f.name
      FROM forum_parents AS fp
      LEFT JOIN forums AS f
        ON fp.forumId = f.id
      WHERE forumId = :forumId;
    ';
    $binds = array(
      'forumId' => $forumId,
    );
    return DB::q($query, $binds)->fetchAll();
  }


}




