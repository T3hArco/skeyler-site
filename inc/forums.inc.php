<?php

class Forums
{

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


}




