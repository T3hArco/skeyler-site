<?php
require '../_.php';
$Page = new Page('stats');

$gamemode = getGet('gamemode');
$mapId = (int) getGet('mapId');

$gamemodesWhitelist = array(
//  'sass' => 'Sassilization',
  'bhop' => 'Bunny Hop',
//  'climb' => 'Climb',
//  'surf' => 'Surf',
//  'deathrun' => 'Deathrun',
);

if (!$gamemode) {

  redirect('/stats.php?gamemode=bhop');
  // TODO: remove this!!!
  exit;

  if (Cache::isFresh('stats', 1)) {
    $data = Cache::load('stats');
  }
  else {
    $data = array();
    //    $query = '
    //      SELECT s.gamemodeId, s.mapId, s.userId, s.timespan, s.timestamp, a.playerCount, g.name, g.shortName
    //      FROM
    //      (
    //        SELECT gamemodeId, mapId, MIN(timespan) AS runTime, COUNT(DISTINCT userId) AS playerCount
    //        FROM stats AS s
    //        GROUP BY gamemodeId, mapId
    //      ) AS a
    //      JOIN stats AS s ON
    //        a.gamemodeId = s.gamemodeId
    //        AND a.mapId = s.mapId
    //        AND a.runTime = s.timespan
    //      JOIN gamemodes AS g ON g.id = s.gamemodeId
    //      ;
    //    ';

    $query = '
      SELECT 2 AS gamemodeId, s.mapId, s.userId, s.timespan, s.timestamp, a.playerCount, g.name, g.shortName
      FROM
      (
        SELECT gamemodeId, mapId, MIN(timespan) AS runTime, COUNT(DISTINCT userId) AS playerCount
        FROM stats AS s
        GROUP BY gamemodeId, mapId
      ) AS a
      JOIN stats AS s ON
        a.gamemodeId = s.gamemodeId
        AND a.mapId = s.mapId
        AND a.runTime = s.timespan
      JOIN gamemodes AS g ON g.id = s.gamemodeId
      ;
    ';


    $rows = DB::q($query)->fetchAll();

    foreach ($rows as $row) {

      if (!isset($data[$row['shortName']])) {
        $data[$row['shortName']] = array(
          'gamemodeName' => $row['name'],
          'gamemodeShortName' => $row['shortName'],
          'playerCount' => $row['playerCount'],
          'record' => array(
            //            'username' => 'OBST!!!',
            'userId' => 0,
            'timespan' => 35999,
            'mapName' => 'every_map_ever!!',
            'mapId' => 0,
          ),
          'maps' => array(),
        );
      }

      if (!isset($data[$row['shortName']]['maps'][$row['mapId']])) {
        $data[$row['shortName']]['maps'][$row['mapId']] = array(
          'mapName' => $row['mapId'], //fixme!!!
          'mapId' => $row['mapId'],
          'recordTimespan' => $row['timespan'],
          'recordUserId' => $row['userId'],
          //          'recordUserName' => $row['username'],
          'recordTimestamp' => $row['timestamp'],
        );
      }
      else {
        if ($data[$row['shortName']]['maps'][$row['mapId']]['recordTimespan'] > $row['timespan']) {
          $data[$row['shortName']]['maps'][$row['mapId']]['recordTimespan'] = $row['timespan'];
          $data[$row['shortName']]['maps'][$row['mapId']]['recordUserId'] = $row['userId'];
          //          $data[$row['shortName']]['maps'][$row['mapId']]['recordUserName'] = $row['username'];
          $data[$row['shortName']]['maps'][$row['mapId']]['recordTimestamp'] = $row['timestamp'];
        }
      }
    }

    foreach ($data as $key => $gamemodeData) {
      $recordData = array(
        //        'username' => 'OBST!!!',
        'userId' => 0,
        'timespan' => 35999,
        'mapName' => 'every_map_ever!!',
        'mapId' => 0,
        'recordTimestamp' => 0,
      );
      foreach ($gamemodeData['maps'] as $mapData) {
        if ($mapData['recordTimestamp'] > $recordData['recordTimestamp']) {
          // a new record. overwrite old record with this one
          $recordData = array(
            //            'username' => $mapData['recordUsername'],
            'userId' => $mapData['recordUserId'],
            'timespan' => $mapData['recordTimespan'],
            'mapName' => $mapData['mapName'],
            'mapId' => $mapData['mapId'],
            'recordTimestamp' => $mapData['recordTimestamp'],
          );
        }
      }
      $data[$key]['record'] = $recordData;
    }
    Cache::save('stats', $data);
  }
  $userIds = array();
  foreach ($data as $gamemodeData) {
    $userIds[] = $gamemodeData['record']['userId'];
  }
  $userIds = array_unique($userIds);
  $users = User::loadIds($userIds);

  $out['stats'] = $data;
  $out['users'] = $users;

  $Page->breadcrumbs = array(
    'Stats' => '/stats.php',
  );
  $Page->header('Stats');
  view($out, 'statsIndex.php');
  exit;
}

if (!in_array($gamemode, array_keys($gamemodesWhitelist))) {
  Notice::error('Invalid gamemode.');
  exit;
}

$gamemodeName = $gamemodesWhitelist[$gamemode];

if (!$mapId) {

  // Stats for BHOP
  $query = '
  SELECT a.mapid, a.recordTime, a.mapname, u.id AS userId, a.playerCount, a.attempts
  FROM
  (
    SELECT br.mapid, MIN(br.time) AS recordTime, bm.mapname, COUNT(DISTINCT br.steamid) AS playerCount, COUNT(br.id) AS attempts
    FROM bh_records br
    JOIN bh_mapids AS bm
      ON br.mapid = bm.id
    /* WHERE br.pb = 1 */
    GROUP BY br.mapid
  ) AS a
  LEFT JOIN bh_records br
    ON a.recordTime = br.time
  LEFT JOIN users AS u
    ON br.steamid = u.steamId
  /* WHERE br.pb = 1 */
  ORDER BY a.mapname ASC, br.date ASC
  ;
';
  $rows = DB::q($query)->fetchAll();

  $records = array();

  $userIds = array(0);
  foreach ($rows as $row) {
    if (!isset($records[$row['mapid']])) {
      $records[$row['mapid']] = array(
        'mapname' => $row['mapname'],
        'userIds' => array($row['userId']),
        'recordTime' => $row['recordTime'],
        'playerCount' => $row['playerCount'],
        'attempts' => $row['attempts'],
      );
      $userIds[] = $row['userId'];
    }
    else {
      $records[$row['mapid']]['userIds'][] = $row['userId'];
      $records[$row['mapid']]['userIds'] = array_unique($records[$row['mapid']]['userIds']);
      $userIds[] = $row['userId'];
    }
  }

  $userIds = array_unique($userIds);
  $users = User::loadIds($userIds);

  $Page->breadcrumbs = array(
    'Stats' => '/stats.php',
    $gamemodeName => '/stats.php?gamemode=' .$gamemode,
  );

  $Page->header($gamemodeName . ' Stats');

  $out = array(
    'recordData' => $records,
    'users' => $users,
    'gamemodeName' => $gamemodeName,
  );
  view($out);

}
else {

  $query = '
    SELECT SQL_CALC_FOUND_ROWS u.id AS userId, MIN(time) AS minTime, COUNT(br.id) AS attempts, level, style
    FROM bh_records AS br
    LEFT JOIN users AS u
      ON br.steamid = u.steamId
    WHERE mapid = :mapId
    GROUP BY br.steamid
    ORDER BY minTime ASC
    ' . DB::writeLimit(20, $pageId) . '
    ;
  ';
  $binds = array(
    'mapId' => $mapId,
  );
  $records = DB::q($query, $binds)->fetchAll();

  $query = 'SELECT FOUND_ROWS() AS rowCount;';
  $rowCountRow = DB::q($query)->fetch();
  $rowCount = (int)$rowCountRow['rowCount'];

  $query = 'SELECT mapname FROM bh_mapids WHERE id = :mapId LIMIT 1;';
  $binds = array(
    'mapId' => $mapId,
  );
  $row = DB::q($query, $binds)->fetch();
  $mapName = $row['mapname'];

  $Page->breadcrumbs = array(
    'Stats' => '/stats.php',
    $gamemodeName => '/stats.php?gamemode=' . $gamemode,
    $mapName => '/stats.php?gamemode=' . $gamemode . '&mapId=' . $mapId,
  );

  $Page->header($mapName . ' Stats');

  $userIds = array_unique(eachField($records, 'userId'));
  $users = User::loadIds($userIds);

  $out = array(
    'records' => $records,
    'users' => $users,
    'rowCount' => $rowCount,
    'gamemodeName' => $gamemodeName,
    'mapName' => $mapName,
  );
  view($out, 'statsPerMap.php');
}
