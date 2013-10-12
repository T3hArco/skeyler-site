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
   * Loads a single user
   *
   * @param int $id
   * @return array steamUser
   */
  public static function load($id)
  {
    global $DB;
    $query = 'SELECT * FROM users WHERE id = ?;';
    return $DB->q($query, $id)->fetch();
  }

  public static function loadIds($userIds)
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


  public static function login($steamId64)
  {
    global $DB, $now;

    $users = Steam::getUserProfile($steamId64);
    if (count($users) == 0) {
      throw new ErrorException('Steam could not find user');
    }
    $user = $users[0];

    $query = '
      INSERT INTO users(steamId64, steamId, name, registerIp, lastLoginIp, registerTimestamp, lastLoginTimestamp, authKey, avatarUrl)
        VALUES(:steamId64, :steamId, :name, :lastLoginIp, :lastLoginIp, :lastLoginTimestamp, :lastLoginTimestamp, :authKey, avatarUrl)
      ON DUPLICATE KEY UPDATE
        name = :name,
        avatarUrl = :avatarUrl,
        lastLoginTimestamp = :lastLoginTimestamp,
        lastLoginIp = :lastLoginIp
    ';

    $binds = array(
      'steamId64' => $user['steamid'],
      'steamId' => Steam::steam64ToSTEAM($user['steamid']),
      'name' => $user['personaname'],
      'lastLoginIp' => '',
      'lastLoginTimestamp' => $now,
      'authKey' => randomStr(64),
      'avatarUrl' => $user['avatar'],
    );

    $DB->q($query, $binds)->fetch();
    return $DB->lastInsertId();

  }


  public static function writeAvatar($url, $type = '')
  {
    // full, medium
    if ($type) {
      $url = substr($url, 0, -4) . '_' . $type . '.jpg';
    }
    return $url;
  }

}