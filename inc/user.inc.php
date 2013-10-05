<?php
class User
{

  const RANK_REGULAR = 0;
  const RANK_MOD = 10;
  const RANK_ADMIN = 50;
  const RANK_DEV = 70;
  const RANK_SUPER = 99;
  const RANK_OWNER = 100;


  /**
   * @param $user
   * @param $rankLevel
   * @return bool
   */
  public function can($user, $rankLevel)
  {
    // if only rank is defined
    if (!$user) {
      $rankLevel = $user;

      // if a non-static call, use $this
      if (isset($this)) {
        $user = $this;
      } else {
        // otherwise grab the $User object
        global $User;
        $user = $User;
      }
    }
    return $user['rank'] >= $rankLevel;
  }


  public function fetchUsers($userIds)
  {
    global $DB;
    $userIds = (array)$userIds;
    if (count($userIds) == 0) {
      return array();
    }

    $whereIn = DB::whereIn($userIds);

    $query = '
      SELECT *
      FROM users
      WHERE id IN(' . $whereIn . ')
      ;
    ';

    return populateIds($DB->q($query)->fetchAll());
  }

}