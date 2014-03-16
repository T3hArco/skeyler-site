<?php



// to delete:
require '../inc/config.inc.php';

//    http://skeyler.com/loading.php?mapname=%m&steamid=%s"

$steam64 = isset($_GET['steamid']) ? $_GET['steamid'] : '';
$mapname = isset($_GET['mapname']) ? $_GET['mapname'] : '';



?>
<link rel="stylesheet" type="text/css" href="<?php echo $Config['mediaServer']; ?>css/loading.css"/>
<script>
  // Called when the loading screen finishes loading all assets.
  function GameDetails(servername, serverurl, mapname, maxplayers, steamid, gamemode) {

  }
  // Called when a file starts downloading. The filename includes the entire path of the file;
  // for example "materials/models/bobsModels/car.mdl".
  function DownloadingFile(fileName) {

  }

  // Called when something happens. This might be "Initialising Game Data", "Sending Client Info", etc.
  function SetStatusChanged(status) {

  }

  // Called at the start, tells us how many files need to be downloaded in total.
  function SetFilesTotal(total) {

  }

  // Called when the number of files to download changes.
  function SetFilesNeeded(needed) {

  }
</script>

<style>


</style>
<body>
<div id="logo"></div>
<p>You are joining <b>The Lounge</b> by Skeyler Servers. Enjoy your stay!</p>

</body>