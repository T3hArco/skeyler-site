<h2>Skeyler Servers</h2>
<br class="clr"/>
<?php foreach ($local['servers'] as $server) : ?>
  <?php if ($server) : ?>
    <?php if (!$server['password'] || User::can(User::RANK_DEV)) : ?>
      <div class="server">
        <div class="name">
          <h3><?php echo ent($server['serverName']); ?></h3>
          <span class="ip"><?php echo ent($server['host'] . ':' . $server['port']); ?><?php echo($server['password'] ? ' <strong>(password protected)</strong>' : ''); ?></span>
      <span class="playerCount"><?php echo $server['playerCount']; ?> / <?php echo $server['playerMax']; ?>
        Players</span>
        </div>
        <div class="players">
          <div class="percent">
            <span class="playerPercent" style="width:<?php echo $server['playerCount'] / max($server['playerMax'], 1) * 100; ?>%;"></span>
          </div>
        </div>
        <div class="details">
          <a href="steam://connect/<?php echo $server['host']; ?>:<?php echo $server['port']; ?>" class="joinServer">Join
            Server</a>
          <dl>
            <dt>Gamemode</dt>
            <dd><?php echo ent($server['gameDescription']); ?></dd>
            <dt>Current Map</dt>
            <dd><?php echo ent($server['map']); ?></dd>
          </dl>
        </div>
      </div>
    <?php endif; ?>
  <?php endif; ?>
<?php endforeach; ?>