<h2>Skeyler Servers</h2>
<?php foreach ($local['servers'] as $server) : ?>
  <div class="server">
    <div class="name">
      <h3><?php echo ent($server['name']); ?></h3>
      <span class="ip"><?php echo ent($server['ip']); ?></span>
    <span class="playerCount"><?php echo $server['currentPlayers']; ?> / <?php echo $server['maxPlayers']; ?>
      Players</span>
    </div>
    <div class="players">
      <div class="percent">
        <span class="playerPercent" style="width:<?php echo $server['currentPlayers'] / $server['maxPlayers'] * 100; ?>%;"></span>
      </div>
    </div>
    <div class="details">
      <a href="#" class="joinServer">Join Server</a>
      <dl>
        <dt>Gamemode</dt>
        <dd><?php echo ent($server['gamemode']); ?></dd>
        <dt>Current Map</dt>
        <dd><?php echo ent($server['map']); ?></dd>
      </dl>
    </div>
  </div>
<?php endforeach; ?>