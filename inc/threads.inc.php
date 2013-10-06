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

}