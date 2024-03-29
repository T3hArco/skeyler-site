<?php
class User
{
  const RANK_GUEST = -1;
  const RANK_REGULAR = 0;
  const RANK_VIP = 5;
  const RANK_MOD = 10;
  const RANK_ADMIN = 50;
  const RANK_DEV = 70;
  const RANK_SUPER = 90;
  const RANK_OWNER = 100;


  /**
   * Loads a single user
   *
   * @param int $id
   * @return array steamUser
   */
  public static function load($id) {
    $query = 'SELECT * FROM users WHERE id = ?;';
    return DB::q($query, $id)->fetch();
  }

  public static function loadIds($userIds) {
    $userIds = (array) $userIds;
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

    return populateIds(DB::q($query)->fetchAll());
  }

  // dupes of the previous 2 functions bc I like this naming better
  public static function getId($userId) {
    return self::load($userId);
  }

  public static function getIds($userIds) {
    return self::loadIds($userIds);
  }

  public static function loadBySteam64($steam64) {
    $query = 'SELECT * FROM users WHERE steamId64 = :steamId64';
    $binds = array(
      'steamId64' => $steam64,
    );
    return DB::q($query, $binds)->fetch();
  }

  /**
   * @param $user
   * @param $rankLevel
   * @return bool
   */
  public static function can($rankLevel, $user = null) {
    // if only rank is defined
    if (!$user) {
      global $User;
      $user = $User;
    }
    return $user['rank'] >= $rankLevel;
  }


  public static function login($steamId64) {
    global $now;

    DB::beginTransaction();

    $users = Steam::getUserProfile($steamId64);
    if (count($users) == 0) {
      throw new ErrorException('Steam could not find user');
    }
    $user = $users[0];

    // in order to prevent auto-increment from reserving id's and making huge gaps
    // in the db, we need to only insert on dupe key update when we don't have
    // data on a user.
    $existingUser = User::loadBySteam64($steamId64);

    if ($existingUser && is_array($existingUser)) {
      $query = '
        UPDATE users
        SET
          name = :name,
          avatarUrl = :avatarUrl,
          lastLoginIp = :lastLoginIp
        WHERE id = :userId
        LIMIT 1;
      ';
      $binds = array(
        'userId' => $existingUser['id'],
        'name' => $user['personaname'],
        'avatarUrl' => $user['avatar'],
        'lastLoginIp' => ip2long($_SERVER['REMOTE_ADDR']),
      );

      DB::q($query, $binds)->fetch();

      DB::commit();

      return $existingUser['id'];

    }
    else {
      $query = '
        INSERT INTO users(steamId64, steamId, name, registerIp, lastLoginIp, registerTimestamp, authKey, avatarUrl)
          VALUES(:steamId64, :steamId, :name, :lastLoginIp, :lastLoginIp, :lastLoginTimestamp, :authKey, :avatarUrl)
        ON DUPLICATE KEY UPDATE
          name = :name,
          avatarUrl = :avatarUrl,
          lastLoginIp = :lastLoginIp
      ';
      $binds = array(
        'steamId64' => $user['steamid'],
        'steamId' => Steam::steam64ToSTEAM($user['steamid']),
        'name' => $user['personaname'],
        'registerIp' => ip2long($_SERVER['REMOTE_ADDR']),
        'lastLoginIp' => ip2long($_SERVER['REMOTE_ADDR']),
        'lastLoginTimestamp' => $now,
        'authKey' => randomStr(64),
        'avatarUrl' => $user['avatar'],
      );
      DB::q($query, $binds)->fetch();

      $lastInsertId = DB::lastInsertId();

      DB::commit();

      return $lastInsertId;
    }
  }


  public static function writeAvatar($url, $type = '') {
    // full, medium
    if(!$url) {
      $url = 'http://media.steampowered.com/steamcommunity/public/images/avatars/f6/f6b5dbb667424c6f7ee8ed413b8ada32935fdd41.jpg';
    }
    if ($type) {
      $url = substr($url, 0, -4) . '_' . $type . '.jpg';
    }
    return $url;
  }

  public static function writeUserName($user) {
    if(is_string($user)) {
      return ent($user);
    }
    return ent($user['name']);
  }

  public static function writeUserLink($user, $options = array()) {
    if(!$user) {
      $user = array(
        'id' => 0,
        'rank' => 0,
        'name' => '<Missing User>',
      );
    }
    if (!isset($options['hideTag'])) {
      $options['hideTag'] = false;
    }
    $out = '<a href="/user.php?userId=' . $user['id'] . '" class="userLink' . (!$options['hideTag'] ? ' tag-' . self::getRankStr($user['rank']) : '') . '">' . User::writeUserName($user) . '</a>';
    return $out;
  }

  // normally you won't use this func. use writeUserLink instead!
  public static function writeRankTag($user, $isLong = false) {

    if (is_int($user)) {
      $rank = $user;
    }
    else {
      $rank = $user['rank'];
    }

    if ($rank == self::RANK_REGULAR) {
      return '';
    }

    $rankShort = self::getRankStr($rank);
    $rankStr = self::getRankStr($rank, $isLong);

    return '<span class="rankTag tag-' . $rankShort . ' ' . ($isLong ? 'longName' : 'shortName') . '">' . $rankStr . '</span>';

    //    return '<img src="' . $Config['mediaServer'] . 'images/tags/' . $rankStr . '.png" class="rankTag" alt="' . $rankStr . '" title="' . ucwords($rankStr) . '" />';
  }

  public static function getRankStr($rank, $isLong = false) {
    if (!$isLong) {
      if ($rank >= self::RANK_OWNER) {
        return 'owner';
      }
      elseif ($rank >= self::RANK_SUPER) {
        return 'super';
      }
      elseif ($rank >= self::RANK_DEV) {
        return 'dev';
      }
      elseif ($rank >= self::RANK_ADMIN) {
        return 'admin';
      }
      elseif ($rank >= self::RANK_MOD) {
        return 'mod';
      }
      elseif ($rank >= self::RANK_VIP) {
        return 'vip';
      }
      elseif ($rank >= self::RANK_REGULAR) {
        return 'reg';
      }
    }
    else {
      if ($rank >= self::RANK_OWNER) {
        return 'Owner';
      }
      elseif ($rank >= self::RANK_SUPER) {
        return 'Super Administrator';
      }
      elseif ($rank >= self::RANK_DEV) {
        return 'Developer';
      }
      elseif ($rank >= self::RANK_ADMIN) {
        return 'Administrator';
      }
      elseif ($rank >= self::RANK_MOD) {
        return 'Moderator';
      }
      elseif ($rank >= self::RANK_VIP) {
        return 'VIP';
      }
      elseif ($rank >= self::RANK_REGULAR) {
        return 'dirtshirt';
      }
    }

    return 'peanutbutter';
  }

  public static function writeModOptions($type) {
    global $local, $User;
    $lis = array();
    switch ($type) {
      case 'forum':
        if (self::can(User::RANK_SUPER)) {
          $lis[] = 'Rename Forum';
          $lis[] = 'Move Forum';
          $lis[] = 'Edit Forum';
          $lis[] = 'Create New Forum';
        }
        break;
      case 'thread':
        if (self::can(User::RANK_ADMIN)) {
          if (!$local['thread']['isClosed']) {
            $lis[] = 'Close Thread';
          }
          else {
            $lis[] = 'Open Thread';
          }
          if (!$local['thread']['isSticky']) {
            $lis[] = 'Sticky Thread';
          }
          else {
            $lis[] = 'UnSticky Thread';
          }
          $lis[] = 'Move Thread';
          $lis[] = 'Delete Thread';
          $lis[] = 'Rename Thread';
        }
        break;
      case 'post':
        if (self::can(User::RANK_MOD)) {
          $lis[] = 'Ban User';
        }
        if (self::can(User::RANK_ADMIN)) {
          $lis[] = 'Edit Post';
          $lis[] = 'Delete Post';
        }
        break;
      case 'user':
        if (self::can(User::RANK_SUPER)) {
          $lis[] = 'Promote User';
          $lis[] = 'Demote User';
        }
        if (self::can(User::RANK_MOD)) {
          $lis[] = 'Ban User';
        }
        break;
      default:
        return '';
    }

    if (count($lis) == 0) {
      return '';
    }

    $out = '<div class="modDropdown"><a href="#" class="sprite modButton"></a><ul class="mod mod-' . $type . '"><li class="userActions">User Actions</li>';
    foreach ($lis as $li) {
      $out .= '<li class="mod-' . spaceToCamel($li) . '" data-mod-type="' . spaceToCamel($li) . '"><a href="#"><span class="sprite mod-' . spaceToCamel($li) . '"></span> ' . $li . '</a></li>';
    }
    $out .= '</ul></div>';

    return $out;
  }

  public static function searchUsers($search, $order = 'name ASC') {
    global $Config, $pageId;

    $wheres = array(
      1
    );

    if ($search) {
      $wheres[] = 'name LIKE :likeSearch';
      $binds['likeSearch'] = '%' . $search . '%';
    }

    $query = '
      SELECT * FROM users
      WHERE ' . implode($wheres, ' AND ') . '
      LIMIT ' . ($Config['usersPerPage'] * ($pageId - 1)) . ', ' . $Config['usersPerPage'] . ';
    ';

    $binds = array(
      'likeSearch' => '%' . $search . '%',
    );

    return populateIds(DB::q($query, $binds)->fetchAll());
  }

  public static function getStaff() {

    $query = '
      SELECT * FROM users
      WHERE rank >= :minRank
      ORDER BY rank DESC, id ASC
    ';
    $binds = array(
      'minRank' => User::RANK_MOD,
    );
    return populateIds(DB::q($query, $binds)->fetchAll());
  }

  // gets all the user's posts along with the rank required to view the post
  public static function getPostsByUser($userId) {
    $query = '
      SELECT p.id, p.timestamp, p.content, p.threadId, t.title, f.id AS forumId, f.name AS forumName, f.visibleRank
      FROM posts AS p
      LEFT JOIN threads AS t ON p.threadId = t.id
      LEFT JOIN forums AS f ON t.forumId = f.id
      WHERE p.userId = :userId
      ORDER BY p.timestamp DESC
      LIMIT 100
      ;
    ';
    $binds = array(
      'userId' => $userId,
    );
    return DB::q($query, $binds)->fetchAll();
  }

  // change the user's rank!!! supers only
  public static function changeUserRank($userId, $newRank) {
    $query = '
      UPDATE users
      SET rank = :newRank
      WHERE id = :userId
      LIMIT 1;
    ';
    $binds = array(
      'userId' => $userId,
      'newRank' => $newRank,
    );
    DB::q($query, $binds);
    return true;
  }

  public static function updateLastActiveTimestamp($userId) {
    global $now;
    $query = '
      UPDATE users
      SET lastActiveForumTimestamp = :now
      WHERE id = :userId
      LIMIT 1;
    ';
    $binds = array(
      'userId' => $userId,
      'now' => $now,
    );
    DB::q($query, $binds);
    return true;
  }

  // returns all users active on the forums in the last 15mins
  public static function loadOnlineUsers() {
    //    return array(
    //      array(
    //        'id' => 1,
    //        'rank' => 0,
    //        'name' => 'asakhgdlakhs!',
    //      ),array(
    //        'id' => 1,
    //        'rank' => 5,
    //        'name' => 'asdt4etdfgDAF',
    //      ),array(
    //        'id' => 1,
    //        'rank' => 10,
    //        'name' => 'asd33DGSD',
    //      ),array(
    //        'id' => 1,
    //        'rank' => 50,
    //        'name' => 'KNOXed!',
    //      ),array(
    //        'id' => 1,
    //        'rank' => 70,
    //        'name' => 'snoipa!',
    //      ),array(
    //        'id' => 1,
    //        'rank' => 90,
    //        'name' => 'TNTag!',
    //      ),array(
    //        'id' => 1,
    //        'rank' => 100,
    //        'name' => 'George!',
    //      ),
    //
    //
    //    );
    global $now;
    $query = '
      SELECT *
      FROM users
      WHERE lastActiveForumTimestamp >= :timestamp
      ORDER BY rank DESC
      LIMIT 51;
    ';
    $binds = array(
      'timestamp' => $now - 15 * 60,
    );
    return DB::q($query, $binds)->fetchAll();
  }

}