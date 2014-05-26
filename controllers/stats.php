<?php
require '../_.php';
$Page = new Page('stats');

$gamemode = getGet('gamemode');

$gamemodesWhitelist = array(
  'sass' => 'Sassilization',
  'bhop' => 'Bunny Hop',
  'climb' => 'Climb',
  'surf' => 'Surf',
  'deathrun' => 'Deathrun',
);

if (!$gamemode) {

  if (Cache::isFresh('stats', 1)) {
    $data = Cache::load('stats');
  }
  else {
    $data = array();
    $query = '
      SELECT s.gamemodeId, s.mapId, s.userId, s.timespan, s.timestamp, a.playerCount, g.name, g.shortName
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

  $Page->header('Stats');
  view($out, 'statsIndex.php');
  exit;
}

if (!in_array($gamemode, array_keys($gamemodesWhitelist))) {
  Notice::error('Invalid gamemode.');
  exit;
}

$Page->header($gamemodesWhitelist[$gamemode] . ' Stats');
$out = array();
view($out);

