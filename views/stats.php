<h2><?php echo $local['gamemodeName']; ?> Stats</h2>
<table class="stats">
  <thead>
  <tr>
    <th><a href="/stats.php?gamemode=bhop&amp;sortType=map<?php echo ($local['sortType'] == 'map' && $local['sortDir'] == 'ASC' ? '&amp;sortDir=desc' : ''); ?>">Map</a></th>
    <th><a href="/stats.php?gamemode=bhop&amp;sortType=players<?php echo ($local['sortType'] == 'players' && $local['sortDir'] == 'ASC' ? '&amp;sortDir=desc' : ''); ?>">Player Count</a></th>
    <th><a href="/stats.php?gamemode=bhop&amp;sortType=attempts<?php echo ($local['sortType'] == 'attempts' && $local['sortDir'] == 'ASC' ? '&amp;sortDir=desc' : ''); ?>">Attempts</a></th>
    <th>Record Holder</th>
    <th><a href="/stats.php?gamemode=bhop&amp;sortType=recordTime<?php echo ($local['sortType'] == 'recordTime' && $local['sortDir'] == 'ASC' ? '&amp;sortDir=desc' : ''); ?>">Fastest Time</a></th>
  </tr>
  </thead>
  <?php foreach ($local['recordData'] as $mapId => $mapRecordData) : ?>
    <tr>
      <td><a href="/stats.php?gamemode=bhop&amp;mapId=<?php echo $mapId; ?>"><?php echo $mapRecordData['mapname']; ?></a></td>
      <td><?php echo $mapRecordData['playerCount']; ?></td>
      <td><?php echo $mapRecordData['attempts']; ?></td>
      <td>
        <?php foreach ($mapRecordData['userIds'] as $userId) : ?>
        <a href="/user.php?userId=<?php echo $userId; ?>" class="userLink">
          <img src="<?php echo User::writeAvatar(isset($local['users'][$userId]) ? $local['users'][$userId]['avatarUrl'] : null); ?>" alt="" class="avatar"/></a><?php echo User::writeUserLink(isset($local['users'][$userId]) ? $local['users'][$userId] : null); ?>
        <?php endforeach; ?>
      </td>
      <td class="recordTime"><?php echo writeTimeLength($mapRecordData['recordTime'], 'record'); ?></td>
    </tr>
  <?php endforeach; ?>
</table>



