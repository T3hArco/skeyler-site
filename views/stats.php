<h2><?php echo $local['gamemodeName']; ?> Stats</h2>
<table class="stats">
  <thead>
  <tr>
    <th>Map</th>
    <th>Player Count</th>
    <th>Attempts</th>
    <th>Record Holder</th>
    <th>Fastest Time</th>
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



