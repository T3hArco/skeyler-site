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
  SELECT f.id, f.name, f.description, fc.postCount, fc.threadCount, f.visibleRank, f.createPostRank, f.createThreadRank, f.lastPostUserId, f.lastPostTimestamp, f.lastPostThreadId
  FROM forums AS f
  LEFT JOIN forum_parents AS fp
    ON fp.forumId = f.id AND fp.depth = 1
  LEFT JOIN forum_caches AS fc
    ON f.id = fc.forumId AND rank = :rank
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
      SELECT f.id, f.name, f.description, fc.postCount, fc.threadCount, f.visibleRank, f.lastPostUserId, f.lastPostTimestamp, f.lastPostThreadId, fp.parentId
      FROM forums AS f
      LEFT JOIN forum_parents AS fp
        ON fp.forumId = f.id AND fp.depth = 1
      LEFT JOIN forum_caches AS fc
        ON f.id = fc.forumId AND rank = :rank
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
        ON f.id = fp.forumId AND fp.depth = 1
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

    DB::beginTransaction();

    $query = '
      SELECT f.id AS forumId, SUM(ts.postsSeen) AS postsSeen
      FROM forum_parents AS fp
      LEFT JOIN forums AS f
        ON fp.parentId = f.id
      LEFT JOIN threads AS t
        ON fp.parentId = t.forumId
      LEFT JOIN thread_seen AS ts
        ON t.id = ts.threadId
      WHERE fp.forumId = :forumId AND ts.userId = :userId AND f.visibleRank <= :rank
      GROUP BY f.id
      ORDER BY fp.depth ASC
    ';
    $binds = array(
      'forumId' => $forumId,
      'userId' => $User['id'],
      'rank' => $User['rank'],
    );
    $rows = DB::q($query, $binds)->fetchAll();

    $query = '
      SELECT f.id AS forumId, ts.postsSeen
      FROM forums AS f
      LEFT JOIN threads AS t
        ON t.forumId = f.id
      LEFT JOIN thread_seen AS ts
        ON t.id = ts.threadId
      WHERE f.id = :forumId AND ts.userId = :userId AND f.visibleRank <= :rank
      LIMIT 1;
    ';
    $binds = array(
      'forumId' => $forumId,
      'userId' => $User['id'],
      'rank' => $User['rank'],
    );
    $currentForumData = DB::q($query, $binds)->fetch();

    array_unshift($rows, $currentForumData);
var_dump($rows);
    $postCount = 0;

    foreach($rows as $row){

      $postCount += $row['postsSeen'];

      $query = '
        INSERT INTO forum_seen (forumId, userId, postsSeen)
        VALUES(:forumId, :userId, :postsSeen)
        ON DUPLICATE KEY UPDATE postsSeen = :postsSeen
      ';
      // who cares about primary keys here? but might want to make it bigint?
      $binds = array(
        'forumId' => $row['forumId'],
        'userId' => $User['id'],
        'postsSeen' => $postCount,
      );
    }

    DB::q($query, $binds);

    DB::commit();
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
    echo 'dont use function recountCounts() anymore. use forceRecacheForumCounts()';
    throw new Exception('dont use function recountCounts() anymore');

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
      INSERT INTO forum_parents(forumId, parentId, depth)
      VALUES(:forumId, :parentId, 1);
    ';
    $binds = array(
      'forumId' => $forumId,
      'parentId' => $parentId,
    );
    DB::q($query, $binds);

    $query = '
      INSERT INTO forum_parents(forumId, parentId, depth)
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

  public static function getParents($forumId) {
    $query = '
      SELECT f.name, f.id
      FROM forum_parents AS fp
      LEFT JOIN forums AS f
        ON fp.parentId = f.id
      WHERE fp.forumId = :forumId
        AND fp.parentId != 0
      ORDER BY fp.depth DESC
      ;
    ';
    $binds = array(
      'forumId' => $forumId,
    );
    return DB::q($query, $binds)->fetchAll();
  }

  public static function forceRecacheForumCounts() {

    DB::beginTransaction();

    // update threads table's counts
    $query = '
      UPDATE threads AS t
        LEFT JOIN
        (
          SELECT threadId, COUNT(*) AS postCount
          FROM posts
          GROUP BY threadId
        )
        AS src
          ON t.id = src.threadId
        SET t.postCount = src.postCount;
    ';
    DB::q($query);

    // update forums table's counts
    $query = '
      UPDATE forums AS f
        LEFT JOIN
        (
          SELECT forumId, COUNT(*) AS threadCount, SUM(postCount) AS postCount
          FROM threads
          GROUP BY forumId
        )
        AS src
          ON f.id = src.forumId
        SET f.threadCount = src.threadCount,
          f.postCount = src.postCount;
    ';
    DB::q($query);

    // inserts a helluva lot of rows into the caches table, ignoring ones that already exist
    $query = '
       INSERT IGNORE INTO forum_caches (forumId, rank)
       SELECT f.id, r.id
         FROM forums AS f
          JOIN ranks AS r
       ;
    ';
    DB::q($query);

    // holy moley look at this freaking query. I tried to optimize it. someone try to optimize it more if you dare
    $query = '
    UPDATE forum_caches AS fc

    LEFT JOIN
    (
      SELECT
        fp.parentId AS forumId,
        r.id AS rank,
        SUM(IF(f.visibleRank <= r.id,f.postCount,0)) + f.postCount AS postCount,
        SUM(IF(f.visibleRank <= r.id,f.threadCount,0)) + f.threadCount AS threadCount
      FROM (
        SELECT forumId, parentId
        FROM forum_parents
        UNION
          SELECT forumId, forumId
          FROM forum_parents
      ) AS fp
      LEFT JOIN forums AS f ON fp.forumId = f.id
      JOIN ranks AS r
      GROUP BY fp.parentId, r.id
    )
    AS src

    ON fc.forumId = src.forumId
      AND fc.rank = src.rank

    SET
      fc.postCount = src.postCount,
      fc.threadCount = src.threadCount
    ;
    ';
    DB::q($query);


    DB::commit();
  }


}




