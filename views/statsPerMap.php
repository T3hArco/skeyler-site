<h2><?php echo $local['mapName']; ?> Stats</h2>
<?php echo writePageNav(null, $local['rowCount'] / 20, null, null, 'right'); ?>
<table class="stats">
  <thead>
  <tr>
    <th>User</th>
    <th>Attempts</th>
    <th>Fastest Time</th>
  </tr>
  </thead>
  <?php foreach ($local['records'] as $record) : ?>
    <tr>
      <td><img src="<?php echo User::writeAvatar(isset($local['users'][$record['userId']]) ? $local['users'][$record['userId']]['avatarUrl'] : null); ?>" alt="" class="avatar"/><?php echo User::writeUserLink(isset($local['users'][$record['userId']]) ? $local['users'][$record['userId']] : null); ?></td>
      <td><?php echo $record['attempts']; ?></td>
      <td class="recordTime"><?php echo writeTimeLength($record['minTime'], 'record'); ?></td>
    </tr>
  <?php endforeach; ?>
</table>
<?php echo writePageNav(null, $local['rowCount'] / 20); ?>