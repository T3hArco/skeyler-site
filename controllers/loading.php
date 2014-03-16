<?php

$dontUpdateLoginTimestamp = true;

require '../_.php';

// sv_loadingurl "http://skeyler.com/loading.php?steamid=%s&mapname=%m"

$steam64 = isset($_GET['steamid']) ? $_GET['steamid'] : '';
$mapname = isset($_GET['mapname']) ? $_GET['mapname'] : '';

$mapname = 'The Lounge';

$user = null;

if (Steam::isValidSteam64($steam64)) {
  $user = User::loadBySteam64($steam64);

  if (!$user) {
    $userTemp = Steam::getUserProfile($steam64);

    if ($userTemp && count($userTemp) == 1) {
      $user = array(
        'steamId' => Steam::steam64ToSTEAM($userTemp[0]['steamid']),
        'name' => $userTemp[0]['personaname'],
        'lastLoginTimestamp' => 0,
        'rank' => 0,
        'money' => 0,
        'playtime' => 0,
        'avatarUrl' => $userTemp[0]['avatar'],
      );
    }
  }
}

if (!$user) {
  $user = array(
    'steamId' => '0:0:1',
    'name' => 'Friend',
    'lastLoginTimestamp' => 0,
    'rank' => 0,
    'money' => 0,
    'playtime' => 0,
    'avatarUrl' => 'http://media.steampowered.com/steamcommunity/public/images/avatars/fe/fef49e7fa7e1997310d705b2a6158ff8dc1cdfeb.jpg',
  );
}

if (!$user['avatarUrl']) {
  $user['avatarUrl'] = 'http://media.steampowered.com/steamcommunity/public/images/avatars/fe/fef49e7fa7e1997310d705b2a6158ff8dc1cdfeb.jpg';
}
if (is_null($user['name'])) {
  $user['name'] = 'Friend';
}


?>
<link rel="stylesheet" type="text/css" href="<?php echo $Config['mediaServer']; ?>css/loading.css"/>


<style>


</style>
<body>
<div class="top-wrapper">
  <div id="logo"></div>
  <p>You are joining <?php echo($mapname ? '<b>' . $mapname . '</b> by' : 'the') ?> Skeyler Servers. Enjoy your stay!</p>

  <div class="loading-bar"></div>
  <p class="loading-message">Loading....</p>
</div>
<div class="bottom-wrapper">
  <div class="user-info">
    <img class="avatar" src="<?php echo User::writeAvatar($user['avatarUrl'], 'full'); ?>"/>

    <div class="steam-id">STEAM_<?php echo $user['steamId']; ?></div>
  </div>
  <div class="welcome-wrapper">
    <h3>Welcome Back, <span class="name"><?php echo ent($user['name']); ?></span>!</h3>

    <div class="money"><?php echo number_format((int) $user['money']); ?></div>
    <div class="rank"><?php echo User::writeRankTag($user); ?></div>
  </div>
</div>
</body>

<script>
  var $loading = document.getElementById('loading-message')[0];
  var filesTotal = 'X';

  // Called when the loading screen finishes loading all assets.
  function GameDetails(servername, serverurl, mapname, maxplayers, steamid, gamemode) {

  }
  // Called when a file starts downloading. The filename includes the entire path of the file;
  // for example "materials/models/bobsModels/car.mdl".
  function DownloadingFile(fileName) {
    $loading.textContent = 'Downloading some sweet stuff: ' + fileName;
  }

  // Called when something happens. This might be "Initialising Game Data", "Sending Client Info", etc.
  function SetStatusChanged(status) {
    $loading.textContent = status;
  }

  // Called at the start, tells us how many files need to be downloaded in total.
  function SetFilesTotal(total) {
    filesTotal = parseInt(total, 10);
  }

  // Called when the number of files to download changes.
  function SetFilesNeeded(needed) {

  }
</script>