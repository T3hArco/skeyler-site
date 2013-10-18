<?php
class User
{

  const RANK_REGULAR = 0;
  const RANK_VIP = 5;
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
  public static function can($rankLevel, $user = null)
  {
    // if only rank is defined
    if (!$user) {
      global $User;
      $user = $User;
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

  public static function writeUserLink($user, $options = array())
  {
    if (!isset($options['hideTag'])) {
      $options['hideTag'] = false;
    }
    $out = '<a href="/user.php?userId=' . $user['id'] . '" class="userLink' . (!$options['hideTag'] ? ' tag-' . self::getRankStr($user['rank']) : '') . '">' . ent($user['name']) . '</a>';
    return $out;
  }

  // normally you won't use this func. use writeUserLink instead!
  public static function writeRankTag($user)
  {
    global $Config;
    return '<img src="' . $Config['mediaServer'] . 'images/tags/' . self::getRankStr($user['rank']) . '.png" class="rankTag" />';
  }

  public static function getRankStr($rank)
  {
    if ($rank >= self::RANK_OWNER) {
      return 'owner';
    } elseif ($rank >= self::RANK_SUPER) {
      return 'super';
    } elseif ($rank >= self::RANK_DEV) {
      return 'dev';
    } elseif ($rank >= self::RANK_ADMIN) {
      return 'admin';
    } elseif ($rank >= self::RANK_MOD) {
      return 'mod';
    } elseif ($rank >= self::RANK_VIP) {
      return 'vip';
    } elseif ($rank >= self::RANK_REGULAR) {
      return 'regular';
    }
    return 'peanutbutter';
  }

  public static function writeModOptions($type)
  {
    global $local;
    $lis = array();
    switch ($type) {
      case 'forum':
        if (self::can(User::RANK_ADMIN)) {
          $lis[] = 'Rename Forum';
          $lis[] = 'Move Forum';
        }
        break;
      case 'thread':
        if (self::can(User::RANK_ADMIN)) {
          if(!$local['thread']['isClosed']) {
            $lis[] = 'Close Thread';
          } else {
            $lis[] = 'Open Thread';
          }
          if(!$local['thread']['isSticky']) {
            $lis[] = 'Sticky Thread';
          } else {
            $lis[] = 'UnSticky Thread';
          }
          $lis[] = 'Move Thread';
          $lis[] = 'Delete Thread';
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
        if (self::can(User::RANK_MOD)) {
          $lis[] = 'Ban User';
        }
        if (self::can(User::RANK_SUPER)) {
          $lis[] = 'Change Rank';
        }
        break;
      default:
        return '';
    }

    if (count($lis) == 0) {
      return '';
    }

    $out = '<div class="modDropdown"><a href="#" class="star"></a><ul class="mod mod-' . $type . '">';
    foreach ($lis as $li) {
      $out .= '<li class="mod-' . spaceToCamel($li) . '" data-mod-type="' . spaceToCamel($li) . '"><a href="#">' . $li . '</a></li>';
    }
    $out .= '</ul></div>';

    return $out;
  }

}